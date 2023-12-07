<?php

namespace App\Http\Controllers\api\v1\Language;

use App\Http\Controllers\Controller;
use App\Http\Resources\Language\LanguageCollection;
use App\Http\Resources\Language\LanguageResource;
use App\Models\Language\Language;
use App\Services\Language\LanguageService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct(
        private readonly LanguageService $languageService
    )
    {
    }

    public function index(): LanguageCollection
    {
        return new LanguageCollection($this->languageService->index());
    }

    public function show(Language $language): LanguageResource
    {
        return new LanguageResource($language);
    }
}
