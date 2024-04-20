<?php

namespace App\Telegram\DTO;

use App\Telegram\Enum\Callback\CallbackGroup;

class CallbackQuery
{
    public CallbackGroup $group;

    public string $type;

    public array $data;

    public function __construct(CallbackGroup $group, string $type, array $data)
    {
        $this->group = $group;
        $this->type = $type;
        $this->data = $data;
    }

    public static function buildJson(CallbackGroup $group, string $type, array $data = []): string
    {
        return json_encode([
            'group' => $group->value,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self(
            CallbackGroup::from($data['group']),
            $data['type'],
            $data['data']
        );
    }

    public function toJson(): string
    {
        return json_encode([
            'group' => $this->group->value,
            'type' => $this->type,
            'data' => $this->data,
        ]);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
