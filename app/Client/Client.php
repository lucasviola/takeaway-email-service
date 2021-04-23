<?php


namespace App\Client;


use App\Client\Providers\EmailProviderResponse;
use App\Exceptions\ProviderNotAvailableException;
use App\Utils\JSONParser;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Client
{
    private \GuzzleHttp\Client $client;

    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    public function post(string $url, array $requestOptions): EmailProviderResponse
    {
        try {
            Log::info('[Client@post] - Message sent to external provider.');

            $response = $this->client->post($url, $requestOptions);

            return new EmailProviderResponse(JSONParser::parseToJson($response->getBody()->getContents()));
        } catch (GuzzleException $e) {
            Log::warning('[Client@post] - Email provider failed. Reason: '
                . $e->getMessage());

            throw new ProviderNotAvailableException('Provider not available. Reason: '
                . $e->getMessage());
        }
    }

}
