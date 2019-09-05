<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\ClientApi;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Sourcya\AddressModule\Models\AddressProxy;
use Sourcya\BoilerplateBox\Http\Resources\UserResource;
use Sourcya\UserModule\Models\UserProxy;
use Sourcya\UserModule\Contracts\User;
use Illuminate\Http\Request;
use Sourcya\BoilerplateBox\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
 * UsersController constructor.
 */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
        $this->middleware('role:Client');
    }

    /**
     * Display the specified user user.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        if (auth()->id() == $user->id) {
            if ($user){
                $message = 'User info has been fetched successfully';
                return new UserResource($user,$message);
            } else {
                $message = 'No such user found';
                return $this->errorResponseHandler($message,404);
            }
        } else {
            $message = 'UnAuthorized';
            return $this->errorResponseHandler($message,401);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request,User $user)
    {
        if (!empty($request->all())) {
            if (auth()->id() == $user->id) {

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

                if (isset($request->default_address_id)) {
                    $validator = Validator::make($request->only('default_address_id'), [
                        'default_address_id' => 'nullable|exists:addresses,id',
                    ]);

                    if ($validator->fails()){
                        return response()->json($validator->validate());
                    }

                    $validator->after(function ($validator) use ($request) {
                        if (AddressProxy::where('id',$request->default_address_id)->first()->user_id != auth()->id()) {
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

                    $validator->after(function ($validator) use ($request) {
                        if (AddressProxy::where('id',$request->default_contact_id)->first()->user_id != auth()->id()) {
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
                $message = 'UnAuthorized';
                return $this->errorResponseHandler($message, 401);
            }
        } else {
            $message = 'Missing some inputs';
            return $this->errorResponseHandler($message, 404);
        }
    }
}
