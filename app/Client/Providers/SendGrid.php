<?php


namespace App\Client\Providers;

use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Utils\JSONParser;

abstract class SendGrid
{
    const URL = 'https://api.sendgrid.com/v3/mail/send';

    public static function buildRequestOptions(Message $message): array {
        $mapper = new MessageMapper();
        $body = $mapper->mapMessageToSendgridMessage($message);
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
