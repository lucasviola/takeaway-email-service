<?php


namespace App\Client\Providers;


use App\Utils\JSONParser;

abstract class Mailjet
{
    const URL = 'https://api.mailjet.com/v3.1/send';

    public static function buildRequestOptions(array $body): array {
        return [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => JSONParser::parseToString($body),
            'debug' => false
        ];
    }
}
