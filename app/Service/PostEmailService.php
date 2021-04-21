<?php

namespace App\Service;

use App\Client\MailjetEmailClient;
use App\Client\SendGridEmailClient;
use App\Exceptions\MailjetNotAvailableException;
use App\Model\Message;

class PostEmailService
{
    private MailjetEmailClient $mailjetClient;
    private SendGridEmailClient $sendGridEmailClient;

    public function __construct(MailjetEmailClient $mailjetClient, SendGridEmailClient $sendGridEmailClient)
    {
        $this->mailjetClient = $mailjetClient;
        $this->sendGridEmailClient = $sendGridEmailClient;
    }

    public function post(Message $message): array
    {
        try {
            return $this->mailjetClient->postMessage($message);
        } catch (MailjetNotAvailableException $e) {
            return $this->sendGridEmailClient->postMessage($message);
        }
    }
}
