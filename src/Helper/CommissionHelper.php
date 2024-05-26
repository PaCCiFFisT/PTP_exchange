<?php

namespace App\Helper;

class CommissionHelper
{
    public static function calculateCommission(string $amount, string $commission): string
    {
        $commission = bcmul($amount, $commission, 3);

        return number_format(ceil($commission * 100) / 100, 2, '.', '');
    }
}
