<?php

namespace CharlesAugust44\NubankPHP\Models;

class BillItem extends Base
{
    public string $category;
    public int $amount;
    public string $transaction_id;
    public int $index;
    public int $charges;
    public string $type;
    public string $title;
    public string $id;
    public string $href;
    public string $post_date;
    public ?string $type_detail;

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

    protected function getClassName(): string
    {
        return self::class;
    }

    public static function getCategories(): array
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
