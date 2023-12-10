<?php

namespace App\Data\Category;

use Spatie\LaravelData\Data;

class CategoryData extends Data
{
    public function __construct(
        public ?int $parent_category_id,
        public int $user_id,
        public ?string $icon,
        public string $name,
        public ?string $description,
    ) {
    }
}
