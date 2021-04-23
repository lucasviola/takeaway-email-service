<?php


namespace App\Client\Providers;

use App\Utils\JSONParser;

abstract class SendGrid
{
    const URL = 'https://api.sendgrid.com/v3/mail/send';

    public static function buildRequestOptions(array $body): array {
        return [
            'headers'  => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('SENDGRID_API_KEY')
            ],
            'body' => JSONParser::parseToString($body),
            'debug' => false
        ];
    }

}
