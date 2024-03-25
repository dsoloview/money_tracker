<?php

namespace App\Data\Category;

use Spatie\LaravelData\Data;

class CategoryData extends Data
{
    public function __construct(
        public ?int $parent_category_id,
        public ?int $icon_id,
        public string $type,
        public string $name,
        public ?string $description,
    ) {
    }
}
