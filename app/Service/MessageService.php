<?php

namespace App\Service;

use App\Client\MailjetClient;

class MessageService
{

    private MailjetClient $mailjetClient
    ;

    public function __construct(MailjetClient $mailjetClient)
    {
        $this->mailjetClient = $mailjetClient;
    }

    public function sendEmail() {
        return $this->mailjetClient->callSendMessage();
    }
}
