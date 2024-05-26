<?php

namespace App\Service\ExchangeRatesApi\Request;

class GetExchangeRateRequest
{
    private const string URL = 'latest';
    private const string METHOD = 'GET';

    public function getUri()
    {
        return self::URL;
    }

    public function getMethod(): string
    {
        return self::METHOD;
    }
}
