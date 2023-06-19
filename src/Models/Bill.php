<?php

namespace CharlesAugust44\NubankPHP\Models;

class Bill extends Base
{
    public ?string $id;
    public string $state;
    public Summary $summary;
    public BillLink $_links;
    public ?array $line_items;

    public const STATE_OPEN = 'open';
    public const STATE_FUTURE = 'future';
    public const STATE_OVERDUE = 'overdue';

    public const CATEGORY_HOUSE = 'Casa';
    public const CATEGORY_EDUCATION = 'Educação';
    public const CATEGORY_ELECTRONICS = 'Eletrônicos';
    public const CATEGORY_RECREATION = 'Lazer';
    public const CATEGORY_OTHER = 'Outros';
    public const CATEGORY_RESTAURANT = 'Restaurante';
    public const CATEGORY_HEALTH = 'Saúde';
    public const CATEGORY_SERVICES = 'Serviços';
    public const CATEGORY_SUPERMARKET = 'Supermercado';
    public const CATEGORY_TRANSPORT = 'Transporte';
    public const CATEGORY_CLOTHING = 'Vestuário';
    public const CATEGORY_TRAVEL = 'Viagem';

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

    public function getStates(): array
    {
        return [
            self::STATE_FUTURE,
            self::STATE_OPEN,
            self::STATE_OVERDUE
        ];
    }

    public function getCategories(): array
    {
        return [
            self::CATEGORY_HOUSE,
            self::CATEGORY_EDUCATION,
            self::CATEGORY_ELECTRONICS,
            self::CATEGORY_RECREATION,
            self::CATEGORY_OTHER,
            self::CATEGORY_RESTAURANT,
            self::CATEGORY_HEALTH,
            self::CATEGORY_SERVICES,
            self::CATEGORY_SUPERMARKET,
            self::CATEGORY_TRANSPORT,
            self::CATEGORY_CLOTHING,
            self::CATEGORY_TRAVEL
        ];
    }
}
