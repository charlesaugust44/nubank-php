<?php

namespace CharlesAugust44\NubankPHP\Models;

class Summary extends Base
{
    public ?int $remaining_balance;
    public string $due_date;
    public string $close_date;
    public ?string $late_interest_rate;
    public int $past_balance;
    public ?string $late_fee;
    public string $effective_due_date;
    public int $total_balance;
    public string $interest_rate;
    public int $interest;
    public int $total_cumulative;
    public int $paid;
    public int $minimum_payment;
    public ?int $remaining_minimum_payment;
    public string $open_date;
    public string $spent_amount;

    protected function getClassName(): string
    {
        return self::class;
    }
}
