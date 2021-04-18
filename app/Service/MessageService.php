<?php

namespace App\Service;

use App\Client\PostEmailAdapter;

class MessageService
{

    private PostEmailAdapter $client;

    public function __construct(PostEmailAdapter $client)
    {
        $this->client = $client;
    }

    public function sendEmail($message) {
        return $this->client->post($message);
    }
}
