<?php


namespace App\Client;


use App\Exceptions\SendGridNotAvailableException;
use App\Utils\JSONParser;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Model\SendGridResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SendGridEmailClient
{
    private MessageMapper $messageMapper;
    private Client $client;

    public function __construct(MessageMapper $messageMapper, Client $client)
    {
        $this->messageMapper = $messageMapper;
        $this->client = $client;
    }

    public function postMessage(Message $message): SendGridResponse
    {
        try {
            $response = $this->client->post('https://api.sendgrid.com/v3/mail/send',
                $this->buildRequestOptions($message));

            $sendgridResponse =
                new SendGridResponse(JSONParser::parseToJson($response->getBody()->getContents()));

            return $sendgridResponse;
        } catch (GuzzleException $e) {
            throw new SendGridNotAvailableException("Sendgrid not available");
        }
    }

    public function buildRequestOptions(Message $message): array {
        return [
            'headers'  => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('SENDGRID_API_KEY')
            ],
            'body' => JSONParser::parseToString($this->messageMapper->mapMessageTosendgridMessage($message)),
            'debug' => false
        ];
    }
}
