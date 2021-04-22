<?php

namespace App\Service;

use App\Client\MailjetEmailClient;
use App\Client\SendGridEmailClient;
use App\Exceptions\MailjetNotAvailableException;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Model\MessageSent;
use Illuminate\Support\Facades\Log;

class PostEmailService
{
    private MailjetEmailClient $mailjetClient;
    private SendGridEmailClient $sendGridEmailClient;
    private MessageMapper $messageMapper;

    public function __construct(MailjetEmailClient $mailjetClient,
                                SendGridEmailClient $sendGridEmailClient,
                                MessageMapper $messageMapper)
    {
        $this->mailjetClient = $mailjetClient;
        $this->sendGridEmailClient = $sendGridEmailClient;
        $this->messageMapper = $messageMapper;
    }

    public function post(Message $message): MessageSent
    {
        try {
            Log::info('[PostEmailService@post] - Posting message to email providers');

            $mailjetResponse =  $this->mailjetClient->postMessage($message);

            $messageSent = $this->messageMapper->mapFromMailjetResponseToMessageSent($mailjetResponse);

            return $messageSent;
        } catch (MailjetNotAvailableException $e) {
            Log::warning('[PostEmailService@post] - Activating e-mail provider fallback');

            $sendGridResponse = $this->sendGridEmailClient->postMessage($message);

            $messageSent = $this->messageMapper->mapFromSendgridResponseToMessageSent($sendGridResponse);

            return $messageSent;
        }
    }
}
