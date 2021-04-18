<?php

namespace App\Service;

use App\Client\MailjetEmailClient;
use App\Model\Message;

class PostEmailService
{
    private MailjetEmailClient $client;

    public function __construct(MailjetEmailClient $mailjetClient)
    {
        $this->client = $mailjetClient;
    }

    public function post(Message $message)
    {
        return $this->client->postMessage($message);
    }
}
