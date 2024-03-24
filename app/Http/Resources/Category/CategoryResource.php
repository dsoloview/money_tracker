<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_category' => new CategoryResource($this->whenLoaded('parentCategory')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'user' => new UserResource($this->whenLoaded('user')),
            'icon' => $this->icon,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
