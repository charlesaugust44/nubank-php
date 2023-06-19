<?php

namespace CharlesAugust44\NubankPHP\Models;

class Href extends Base
{
    public string $href;

    protected function getClassName(): string
    {
        return self::class;
    }
}
