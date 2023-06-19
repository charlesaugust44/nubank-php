<?php

namespace CharlesAugust44\NubankPHP\Models;

class BillLink extends Base
{
    public Href $self;
    public Href $barcode;
    public Href $boleto_email;

    protected function getClassName(): string
    {
        return self::class;
    }
}
