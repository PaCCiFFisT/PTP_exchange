<?php

namespace App\Handler;

use App\Helper\CommissionHelper;

class DepositHandler
{
    // 0.03%
    private const string DEFAULT_DEPOSIT_COMMISSION = '0.0003';

    public function calculateDepositCommission(string $amount): string
    {
        return CommissionHelper::calculateCommission($amount, self::DEFAULT_DEPOSIT_COMMISSION);
    }
}
