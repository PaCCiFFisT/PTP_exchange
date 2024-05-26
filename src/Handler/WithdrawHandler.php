<?php

namespace App\Handler;

use App\Enum\ClientTypeEnum;
use App\Helper\CommissionHelper;

class WithdrawHandler
{
    // 0.5%
    private const string BUSINESS_WITHDRAW_COMMISSION = '0.005';

    // 0.3%
    private const string PRIVATE_WITHDRAW_COMMISSION = '0.003';

    private const int FREE_WITHDRAW_LIMIT = 3;

    private const string FREE_WITHDRAW_AMOUNT = '1000.00';

    private array $clientWithdraws = [];

    public function calculateWithdrawCommission(array $withdrawRequest): string
    {
        $operationDate = $withdrawRequest[0];
        $clientId = $withdrawRequest[1];
        $clientType = $withdrawRequest[2];
        $amount = $withdrawRequest[4];

        if ($clientType === ClientTypeEnum::Business->value) {
            return CommissionHelper::calculateCommission($amount, self::BUSINESS_WITHDRAW_COMMISSION);
        }

        $withdrawDate = new \DateTime($operationDate);

        if (!isset($this->clientWithdraws[$clientId]) || $this->clientWithdraws[$clientId]['renewLimitsAt'] < $withdrawDate) {
            $this->initClientWithdraws($clientId, $withdrawDate);
        }

        if ($this->clientWithdraws[$clientId]['freeLimit'] <= 0) {
            $this->updateClientWithdraws($clientId, $amount);

            return CommissionHelper::calculateCommission($amount, self::PRIVATE_WITHDRAW_COMMISSION);
        }

        if ($this->clientWithdraws[$clientId]['count'] > self::FREE_WITHDRAW_LIMIT) {
            $this->updateClientWithdraws($clientId, $amount);

            return CommissionHelper::calculateCommission($amount, self::PRIVATE_WITHDRAW_COMMISSION);
        }

        $limitDiff = bcsub($amount, $this->clientWithdraws[$clientId]['freeLimit']);
        $this->updateClientWithdraws($clientId, $amount);

        if ($limitDiff > 0) {
            return CommissionHelper::calculateCommission($limitDiff, self::PRIVATE_WITHDRAW_COMMISSION);
        }

        return '0.00';
    }

    private function updateClientWithdraws(string $clientId, string $amount): void
    {
        ++$this->clientWithdraws[$clientId]['count'];
        $this->clientWithdraws[$clientId]['freeLimit'] = bcsub($this->clientWithdraws[$clientId]['freeLimit'], $amount, 2);
    }

    private function initClientWithdraws(string $clientId, \DateTime $withdrawDate): void
    {
        $this->clientWithdraws[$clientId] = [
            'count' => 1,
            'freeLimit' => self::FREE_WITHDRAW_AMOUNT,
            'renewLimitsAt' => $withdrawDate->modify('Sunday this week')->setTime(23, 59, 59),
        ];
    }
}
