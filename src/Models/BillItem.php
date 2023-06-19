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

    protected function getClassName(): string
    {
        return self::class;
    }
}
