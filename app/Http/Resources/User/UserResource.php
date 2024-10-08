<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\User\UserSettings\UserSettingsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'balance' => $this->when('balance', $this->balance),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'settings' => new UserSettingsResource($this->whenLoaded('settings')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
