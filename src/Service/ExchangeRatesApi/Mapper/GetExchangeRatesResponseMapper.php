<?php

namespace App\Service\ExchangeRatesApi\Mapper;

use App\Service\ExchangeRatesApi\Response\GetExchangeRateResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GetExchangeRatesResponseMapper
{
    public function mapSuccessfulResponse(ResponseInterface $response): GetExchangeRateResponse
    {
        $response = json_decode($response->getContent(), true);

        return new GetExchangeRateResponse($response['rates']);
    }

    public function mapExceptionToExceptionResponse(TransportExceptionInterface $e): GetExchangeRateResponse
    {
        return new GetExchangeRateResponse([], $e->getMessage());
    }
}
