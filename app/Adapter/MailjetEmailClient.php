<?php


namespace App\Adapter;


use App\Mapper\MessageMapper;
use App\Model\Message;
use GuzzleHttp\Client;

class MailjetEmailClient
{
    private MessageMapper $messageMapper;
    private Client $client;

    public function __construct(MessageMapper $messageMapper, Client $client)
    {
        $this->messageMapper = $messageMapper;
        $this->client = $client;
    }

    public function postMessage(Message $message)
    {
        $response = $this->client->post(env('MAILJET_MESSAGE_URL'),
            $this->buildRequestOptions($message));

        return $response->getBody();
    }

    public function buildRequestOptions(Message $message): array {
        return [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($this->messageMapper->mapMessageToMailjetMessage($message)),
            'debug' => false
        ];
    }
}
