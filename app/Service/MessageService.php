<?php

namespace App\Service;

use App\Client\GenericClient;
use App\Mapper\MessageMapper;

class MessageService
{

    private GenericClient $client;

    public function __construct(GenericClient $client)
    {
        $this->client = $client;
    }

    public function sendEmail($message) {
        return $this->client->post($message);
    }
}
