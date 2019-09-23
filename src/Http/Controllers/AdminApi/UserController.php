<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\AdminApi;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Sourcya\BoilerplateBox\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sourcya\BoilerplateBox\Http\Resources\UserCollection;
use Sourcya\BoilerplateBox\Http\Resources\UserResource;
use Sourcya\UserModule\Models\UserProxy;
use Sourcya\UserModule\Contracts\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Sourcya\ContactModule\Models\ContactProxy;
use Sourcya\AddressModule\Models\AddressProxy;
use Sourcya\AddressModule\Models\CountryProxy;
use Sourcya\AddressModule\Models\StateProxy;
use Sourcya\AddressModule\Models\CityProxy;
class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
        $this->middleware('permission:View User', ['only' => ['index','show','search']]);
        $this->middleware('permission:Create User', ['only' => ['create','store']]);
        $this->middleware('permission:Edit User', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete User', ['only' => ['destroy']]);

    }

    /**
     * @return UserCollection
     */
    public function index()
    {
        $users = UserProxy::latest()->paginate(12);
        $message = 'All users info has been fetched successfully';
        return new UserCollection($users, $message);
    }

    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|UserResource
     */
    public function show($userId)
    {
        if (UserProxy::where('id',$userId)->exists()){
            $user = UserProxy::find($userId);
            $message = 'User info has been fetched successfully';
            return new UserResource($user, $message);
        } else {
            $message = 'No such user found';
            return $this->errorResponseHandler($message, 404);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|UserCollection
     */
    public function search()
    {
        $users = UserProxy::search(Input::get('query'), null, true)->paginate(12);
        if ($users->count()){
            $message = 'Search results has been fetched successfully';
            return new UserCollection($users,$message);
        } else {
            $message = 'No search results';
            return $this->errorResponseHandler($message,404);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles.*' => 'required|exists:roles,name|not_in:Agent',
        ]);

        $user = UserProxy::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->syncRoles($request->roles);

        if ($user) {
            $message = 'User has been created successfully';
            return (new UserResource($user, $message))->response()->setStatusCode(201);
        } else {
            $message = 'Internal server error';
            return $this->errorResponseHandler($message, 500);
        }
    }

    /**
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request,$userId)
    {
        if (!empty($request->all())) {
            $user = UserProxy::find($userId);
            if (isset($request->first_name)) {
                $this->validate($request, [
                    'first_name' => 'required|string|max:191',
                ]);
                $first_name = $request->first_name;
                $user->update(['first_name' => $first_name]);
            }

            if (isset($request->last_name)) {
                $this->validate($request, [
                    'last_name' => 'required|string|max:191',
                ]);
                $last_name = $request->last_name;
                $user->update(['last_name' => $last_name]);
            }

            if (isset($request->email)) {
                $this->validate($request, [
                    'email' => 'required|email|max:191|unique:user,email,'.$user->id,
                ]);
                $email = $request->email;
                $user->update(['email' => $email]);
            }

            if (isset($request->roles)) {
                $this->validate($request, [
                    'roles.*' => 'required|exists:roles,name|not_in:Agent',
                ]);
                $user->syncRoles($request->roles);
            }

            if (isset($request->default_address_id)) {
                $validator = Validator::make($request->only('default_address_id'), [
                    'default_address_id' => 'nullable|exists:addresses,id',
                ]);

                if ($validator->fails()){
                    return response()->json($validator->validate());
                }

                $validator->after(function ($validator) use ($request,$user) {
                    if (AddressProxy::where('id',$request->default_address_id)->first()->user_id != $user->id) {
                        $validator->errors()->add('default_address_id', 'default_address_id should be owned by this user');
                    }
                })->validate();

                $user->update(['address_id' => $request->default_address_id]);
            }

            if (isset($request->default_contact_id)) {
                $validator = Validator::make($request->only('default_contact_id'), [
                    'default_contact_id' => 'nullable|exists:contacts,id',
                ]);

                if ($validator->fails()){
                    return response()->json($validator->validate());
                }

                $validator->after(function ($validator) use ($request,$user) {
                    if (AddressProxy::where('id',$request->default_contact_id)->first()->user_id != $user->id) {
                        $validator->errors()->add('default_contact_id', 'default_contact_id should be owned by this user');
                    }
                })->validate();

                $user->update(['contact_id' => $request->default_contact_id]);
            }

            if ($user) {
                $message = 'User has been updated successfully';
                return (new UserResource($user, $message))->response()->setStatusCode(202);
            } else {
                $message = 'Internal server error';
                return $this->errorResponseHandler($message, 500);
            }

        } else {
            $message = 'Missing some inputs';
            return $this->errorResponseHandler($message, 404);
        }
    }

    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($userId)
    {
        if (UserProxy::where('id',$userId)->exists()) {
            $user = UserProxy::find($userId);
            if ($user->hasRole('Admin')) {
                $message = 'Can\'t delete Admin user';
                return $this->errorResponseHandler($message, 422);
            }
            $user->delete();
            $message = 'User has been deleted successfully';
            return response()->json(['status' => 'success', 'message' => $message]);
        } else {
            $message = 'No such user found';
            return $this->errorResponseHandler($message, 404);
        }
    }
}
