<?php


namespace App\Client\Providers;


use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Utils\JSONParser;

abstract class Mailjet
{
    const URL = 'https://api.mailjet.com/v3.1/send';

    public static function buildRequestOptions(Message $message): array {
        $mapper = new MessageMapper();
        $requestBody = $mapper->mapMessageToMailjetMessage($message);
        return [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => JSONParser::parseToString($requestBody),
            'debug' => false
        ];
    }
}
