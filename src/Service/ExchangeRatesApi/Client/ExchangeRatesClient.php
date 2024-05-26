<?php

namespace App\Service\ExchangeRatesApi\Client;

use App\Service\ExchangeRatesApi\Mapper\GetExchangeRatesResponseMapper;
use App\Service\ExchangeRatesApi\Request\GetExchangeRateRequest;
use App\Service\ExchangeRatesApi\Response\GetExchangeRateResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ExchangeRatesClient
{
    private const string ACCESS_KEY_PARAM = '?access_key=';

    public function __construct(
        private HttpClientInterface $client,
        private string $ExchangeRatesApiBaseUrl,
        private string $ExchangeRatesApiKey,
        private GetExchangeRatesResponseMapper $responseMapper,
    ) {
    }

    public function getRates(GetExchangeRateRequest $request): GetExchangeRateResponse
    {
        try {
            $response = $this->client->request(
                $request->getMethod(),
                $this->ExchangeRatesApiBaseUrl.$request->getUri().self::ACCESS_KEY_PARAM.$this->ExchangeRatesApiKey
            );

            $response = $this->responseMapper->mapSuccessfulResponse($response);
        } catch (TransportExceptionInterface $e) {
            $response = $this->responseMapper->mapExceptionToExceptionResponse($e);
        }

        return $response;
    }
}
