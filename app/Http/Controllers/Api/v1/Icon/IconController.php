<?php

namespace App\Http\Controllers\Api\v1\Icon;

use App\Http\Controllers\Controller;
use App\Http\Resources\Icon\IconCollection;
use App\Http\Resources\Icon\IconResource;
use App\Models\Icon\Icon;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Icon')]
#[Authenticated]
class IconController extends Controller
{
    #[Endpoint('List of icons')]
    #[ResponseFromApiResource(IconCollection::class, Icon::class)]
    public function index(): IconCollection
    {
        return new IconCollection(Icon::all());
    }

    #[Endpoint('Show icon')]
    #[ResponseFromApiResource(IconResource::class, Icon::class)]
    public function show(Icon $icon): IconResource
    {
        return new IconResource($icon);
    }
}
