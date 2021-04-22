<?php


namespace App\Client;


use App\Exceptions\SendGridNotAvailableException;
use App\Utils\JSONParser;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Model\SendGridResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

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
            $sendgridRequest = $this->messageMapper->mapMessageTosendgridMessage($message);

            Log::info('[SendgridClient@postMessage] - Posting message to SendGrid. Payload: '
                . JSONParser::parseToString($sendgridRequest));

            $response = $this->client->post('https://api.sendgrid.com/v3/mail/send',
                $this->buildRequestOptions($sendgridRequest));

            return new SendGridResponse(JSONParser::parseToJson($response->getBody()->getContents()));
        } catch (GuzzleException $e) {
            Log::warning('[SendgridClient@postMessage] - Sendgrid failed. Reason: '
                . $e->getMessage());

            throw new SendGridNotAvailableException("Sendgrid not available");
        }
    }

    public function buildRequestOptions(array $body): array {
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
