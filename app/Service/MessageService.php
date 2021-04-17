<?php

namespace App\Service;

use App\Client\GenericClient;

class MessageService
{

    private GenericClient $genericClient;

    public function __construct(GenericClient $mailjetClient)
    {
        $this->genericClient = $mailjetClient;
    }

    public function sendEmail($message) {
        return $this->genericClient->post($message);
    }
}
