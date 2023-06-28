<?php

namespace CharlesAugust44\NubankPHP\Models;

class Bill extends Base
{
    public ?string $id = null;
    public string $state;
    public Summary $summary;
    public BillLink $_links;
    public ?array $line_items = null;

    public const STATE_OPEN = 'open';
    public const STATE_FUTURE = 'future';
    public const STATE_OVERDUE = 'overdue';

    public function __construct(object|array|string $data = null)
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        if (is_object($data) && property_exists($data, 'bill')) {
            $data = $data->bill;
        }

        parent::__construct($data);
    }


    protected function getClassName(): string
    {
        return self::class;
    }

    protected function getArrayType(string $key): ?string
    {
        return match ($key) {
            'line_items' => BillItem::class,
            default => null
        };
    }

    public static function getStates(): array
    {
        return [
            self::STATE_FUTURE,
            self::STATE_OPEN,
            self::STATE_OVERDUE
        ];
    }
}
