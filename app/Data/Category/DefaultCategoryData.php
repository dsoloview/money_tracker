<?php

namespace App\Data\Category;

use Spatie\LaravelData\Data;

class DefaultCategoryData extends Data
{
    public function __construct(
        public string $name,
        public string $type
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type']
        );
    }
}
