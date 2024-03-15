<?php

namespace App\Http\Controllers\Api\v1\Role;

use App\Http\Controllers\Controller;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Spatie\Permission\Models\Role;

#[Group('Role')]
#[Authenticated]
class RoleController extends Controller
{
    #[Endpoint('List of roles')]
    #[ResponseFromApiResource(RoleCollection::class, Role::class)]
    public function index(): RoleCollection
    {
        $this->authorize('viewAny', Role::class);
        return new RoleCollection(Role::all());
    }

    #[Endpoint('Show role')]
    #[ResponseFromApiResource(RoleResource::class, Role::class)]
    public function show(Role $role): RoleResource
    {
        $this->authorize('view', $role);
        return new RoleResource($role);
    }
}
