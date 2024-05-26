<?php

namespace App\Service\ExchangeRatesApi\Response;

readonly class GetExchangeRateResponse
{
    public function __construct(private array $rates, private ?string $error = null)
    {
    }

    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRate(string $currency): float
    {
        return $this->rates[$currency];
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
