<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\User\UserSetting\UserSettingResource;
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
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'user_settings' => UserSettingResource::collection($this->whenLoaded('userSettings')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
