<?php

namespace CharlesAugust44\NubankPHP\Models;
/** @property Bill[] $bills */
/** @property string[][] $bills */
class BillSummary extends Base
{
    public array $bills;
    public array $_links;

    protected function getClassName(): string
    {
        return self::class;
    }

    protected function getArrayType(string $key): ?string
    {
        return match ($key) {
            'bills' => Bill::class,
            '_links' => 'array',
            default => null
        };
    }
}
