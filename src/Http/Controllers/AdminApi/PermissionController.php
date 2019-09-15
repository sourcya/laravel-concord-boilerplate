<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\AdminApi;

use Sourcya\BoilerplateBox\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sourcya\BoilerplateBox\Http\Resources\PermissionCollection;
use Sourcya\BoilerplateBox\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
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
     * @return PermissionCollection
     */
    public function index()
    {
        $permissions = Permission::latest()->paginate(12);

        $message = 'All permissions has been fetched successfully';
        return new PermissionCollection($permissions, $message);
    }


}
