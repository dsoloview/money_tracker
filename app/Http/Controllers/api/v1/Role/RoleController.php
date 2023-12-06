<?php

namespace App\Http\Controllers\api\v1\Role;

use App\Http\Controllers\Controller;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): RoleCollection
    {
        return new RoleCollection(Role::all());
    }

    public function show(Role $role): RoleResource
    {
        return new RoleResource($role);
    }
}
