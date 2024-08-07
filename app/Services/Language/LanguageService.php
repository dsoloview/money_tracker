<?php

namespace App\Services\Language;

use App\Models\Language\Language;
use Illuminate\Support\Collection;

class LanguageService
{
    public function index(): Collection
    {
        return \Cache::remember('languages', 60 * 60 * 24, function () {
            return Language::all();
        });
    }
}
