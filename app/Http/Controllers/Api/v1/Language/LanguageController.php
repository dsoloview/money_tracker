<?php

namespace App\Http\Controllers\Api\v1\Language;

use App\Http\Controllers\Controller;
use App\Http\Resources\Language\LanguageCollection;
use App\Http\Resources\Language\LanguageResource;
use App\Models\Language\Language;
use App\Services\Language\LanguageService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Language')]
#[Authenticated]
class LanguageController extends Controller
{
    public function __construct(
        private readonly LanguageService $languageService
    ) {
    }

    #[Endpoint('List of languages')]
    #[ResponseFromApiResource(LanguageCollection::class, Language::class)]
    public function index(): LanguageCollection
    {
        return new LanguageCollection($this->languageService->index());
    }

    #[Endpoint('Show language')]
    #[ResponseFromApiResource(LanguageResource::class, Language::class)]
    public function show(Language $language): LanguageResource
    {
        return new LanguageResource($language);
    }
}
