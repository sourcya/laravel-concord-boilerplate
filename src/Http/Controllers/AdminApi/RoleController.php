<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\AdminApi;

use Sourcya\BoilerplateBox\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sourcya\BoilerplateBox\Http\Resources\RoleCollection;
use Sourcya\BoilerplateBox\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
        $this->middleware('role:Admin');
    }

    /**
     * @return RoleCollection
     */
    public function index()
    {
        $roles = Role::latest()->paginate(12);

        $message = 'All roles has been fetched successfully';
        return new RoleCollection($roles, $message);
    }

    /**
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse|RoleResource
     */
    public function show($roleId)
    {
        if (Role::where('id',$roleId)->exists()){
            $role = Role::find($roleId);
            $message = 'Role has been fetched successfully';
            return new RoleResource($role, $message);
        } else {
            $message = 'No such role found';
            return $this->errorResponseHandler($message, 404);
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
            'name'=>'required|unique:roles,name',
            'permissions' =>'nullable|exists:permissions,name',
            ]
        );

        $role = Role::create(['name' => $request->name]);

        if(isset($request->permissions)){
            $role->syncPermissions($request->permissions);
        }

        if ($role){
            $message = 'Role has been created Successfully';
            return (new RoleResource($role, $message))->response()->setStatusCode(201);
        } else {
            $message = 'Internal Server Error';
            return $this->errorResponseHandler($message, 500);
        }
    }

    /**
     * @param Request $request
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request,$roleId)
    {
        if (Role::where('id',$roleId)->exists()) {
            $this->validate($request, [
                'name' => 'required|unique:roles,name,' . $roleId,
                'permissions' => 'nullable|exists:permissions,name',
            ]);

            $role = Role::find($roleId);
            $role->update(['name' => $request->name]);

            if (isset($request->permissions)) {
                $role->syncPermissions($request->permissions);
            }

            $message = 'Role has been updated Successfully';
            return (new RoleResource($role, $message))->response()->setStatusCode(202);
        } else {
            $message = 'No such role found';
            return $this->errorResponseHandler($message, 404);
        }
    }

    /**
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($roleId)
    {
        if (Role::where('id',$roleId)->exists()) {
            $role = Role::find($roleId);
            if ($role->name == "Admin") {
                $message = 'Can\'t delete default Role';
                return $this->errorResponseHandler($message, 422);
            }
            $role->delete();
            $message = 'Role has been deleted successfully';
            return response()->json(['status' => 'success', 'message' => $message]);
        } else {
            $message = 'No such role found';
            return $this->errorResponseHandler($message, 404);
        }
    }
}
