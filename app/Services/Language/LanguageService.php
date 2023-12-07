<?php

namespace App\Services\Language;

use App\Models\Language\Language;

class LanguageService
{
    public function index()
    {
        return \Cache::remember('languages', 60 * 60 * 24, function () {
            return Language::all();
        });
    }
}
