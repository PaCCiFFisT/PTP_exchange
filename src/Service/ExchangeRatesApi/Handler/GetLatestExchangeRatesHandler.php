<?php

namespace App\Service\ExchangeRatesApi\Handler;

use App\Service\ExchangeRatesApi\Client\ExchangeRatesClient;
use App\Service\ExchangeRatesApi\Request\GetExchangeRateRequest;
use App\Service\ExchangeRatesApi\Response\GetExchangeRateResponse;

class GetLatestExchangeRatesHandler
{
    public function __construct(private ExchangeRatesClient $exchangeRatesClient)
    {
    }

    public function getRates(): GetExchangeRateResponse
    {
        return $this->exchangeRatesClient->getRates(new GetExchangeRateRequest());
    }
}
