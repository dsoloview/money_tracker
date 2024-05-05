<?php

namespace App\Telegram\Enum\Import;

enum ImportMode: int
{
    case CREATE_ABSENT_ENTITIES = 1;
    case NOT_CREATE_ABSENT_ENTITIES = 2;

    public static function validateImportMode(string $value): bool
    {
        return in_array($value, [self::CREATE_ABSENT_ENTITIES->value, self::NOT_CREATE_ABSENT_ENTITIES->value]);
    }

    public function isCreateAbsentEntities(): bool
    {
        return $this === self::CREATE_ABSENT_ENTITIES;
    }
    
    public function isNotCreateAbsentEntities(): bool
    {
        return $this === self::NOT_CREATE_ABSENT_ENTITIES;
    }
}
