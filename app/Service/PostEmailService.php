<?php

namespace App\Service;

use App\Client\Client;
use App\Client\Providers\Mailjet;
use App\Client\Providers\SendGrid;
use App\Exceptions\ProviderNotAvailableException;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Model\MessageSent;
use Illuminate\Support\Facades\Log;

class PostEmailService
{
    private Client $client;
    private MessageMapper $messageMapper;

    public function __construct(Client $client, MessageMapper $messageMapper)
    {
        $this->client = $client;
        $this->messageMapper = $messageMapper;
    }

    public function post(Message $message): MessageSent
    {
        try {
            Log::info('[PostEmailService@post] - Posting message to email providers');

            $mailjetResponse =  $this->client->post(
                Mailjet::URL,
                Mailjet::buildRequestOptions($message->getAttributes()));

            $messageSent = $this->messageMapper->mapFromMailjetResponseToMessageSent($mailjetResponse);

            return $messageSent;
        } catch (ProviderNotAvailableException $e) {
            Log::warning('[PostEmailService@post] - Activating e-mail provider fallback');

            $sendGridResponse = $this->client->post(SendGrid::URL,
                SendGrid::buildRequestOptions($message->getAttributes()));

            $messageSent = $this->messageMapper->mapFromSendgridResponseToMessageSent($sendGridResponse);

            return $messageSent;
        }
    }
}
