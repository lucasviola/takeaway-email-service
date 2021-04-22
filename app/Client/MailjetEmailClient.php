<?php


namespace App\Client;


use App\Exceptions\MailjetNotAvailableException;
use App\Mapper\MessageMapper;
use App\Model\MailjetResponse;
use App\Model\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MailjetEmailClient
{
    private MessageMapper $messageMapper;
    private Client $client;

    public function __construct(MessageMapper $messageMapper, Client $client)
    {
        $this->messageMapper = $messageMapper;
        $this->client = $client;
    }

    public function postMessage(Message $message): MailjetResponse
    {
        try {
            $response = $this->client->post('https://api.mailjet.com/v3.1/send',
                $this->buildRequestOptions($message));

            $mailjetResponse =
                new MailjetResponse(json_decode($response->getBody()->getContents(), true));

            return $mailjetResponse;
        } catch (GuzzleException $e) {
            throw new MailjetNotAvailableException("Mailjet not available");
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
