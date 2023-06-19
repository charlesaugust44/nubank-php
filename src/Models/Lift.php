<?php

namespace CharlesAugust44\NubankPHP\Models;

class Lift extends Base
{
    public string $access_token;
    public string $token_type;
    public array $_links;

    protected function getClassName(): string
    {
        return self::class;
    }

    protected function getArrayType(string $key): ?string
    {
        return match ($key) {
            '_links' => 'array',
            default => null
        };
    }
}
