<?php


namespace App\Client;


use App\Exceptions\EmailProviderNotAvailableException;
use App\Mapper\MessageMapper;
use App\Model\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
        try {
            $response = $this->client->post(env('MAILJET_MESSAGE_URL'),
                $this->buildRequestOptions($message));

            return $response->getBody();
        } catch (GuzzleException $e) {
            throw new EmailProviderNotAvailableException("Mailjet not available");
        }
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
