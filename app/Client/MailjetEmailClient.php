<?php


namespace App\Client;


use App\Exceptions\MailjetNotAvailableException;
use App\Utils\JSONParser;

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
            $mailjetRequest = $this->messageMapper->mapMessageToMailjetMessage($message);

            Log::info('[MailjetClient@postMessage] - Posting message to Mailjet. Payload: '
                . JSONParser::parseToString($mailjetRequest));

            $mailjetResponse = $this->client->post('https://api.mailjet.com/v3.1/send',
                $this->buildRequestOptions($mailjetRequest));

            return new MailjetResponse(JSONParser::parseToJson($mailjetResponse->getBody()->getContents()));
        } catch (GuzzleException $e) {
            Log::warning('[MailjetClient@postMessage] - Mailjet failed. Reason: '
                . $e->getMessage());

            throw new MailjetNotAvailableException("Mailjet not available");
        }
    }

    public function buildRequestOptions(array $body): array {
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
