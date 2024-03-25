<?php

namespace App\Http\Resources\Icon;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Icon\Icon */
class IconResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
            'name' => $this->name,
        ];
    }
}
