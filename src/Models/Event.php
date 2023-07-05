<?php

namespace CharlesAugust44\NubankPHP\Models;
/** @property array[] $_links */
/** @property string[] $_links */
class Event extends Base
{
    public string $id;
    public string $time;

    public ?string $description;
    public string $title;
    public string $category;
    public ?int $amount;
    public ?string $href;
    public ?array $_links;
    public ?array $details;
    public ?string $account;
    public ?int $amount_without_iof;
    public ?string $source;
    public ?bool $tokenized;

    protected function getClassName(): string
    {
        return self::class;
    }

    protected function getArrayType(string $key): ?string
    {
        return match ($key) {
            '_links' => 'array',
            'details' => 'string',
            default => null
        };
    }


}
