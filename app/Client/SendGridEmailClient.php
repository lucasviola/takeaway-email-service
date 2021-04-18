<?php


namespace App\Client;


use App\Exceptions\SendGridNotAvailableException;
use App\Mapper\MessageMapper;
use App\Model\Message;
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

    public function postMessage(Message $message)
    {
        try {
            $response = $this->client->post('https://api.sendgrid.com/v3/mail/send',
                $this->buildRequestOptions($message));

            $sendgridResponse = json_decode($response->getBody()->getContents());

            return $this->messageMapper->mapSendgridResponseToMessageResponse();
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
            'body' => json_encode($this->messageMapper->mapMessageTosendgridMessage($message)),
            'debug' => false
        ];
    }
}
