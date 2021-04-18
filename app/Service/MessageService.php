<?php

namespace App\Service;

use App\Client\PostEmailService;
use Psr\Http\Message\StreamInterface;

class MessageService
{

    private PostEmailService $client;

    public function __construct(PostEmailService $client)
    {
        $this->client = $client;
    }

    public function sendEmail($message): StreamInterface
    {
        return $this->client->post($message);
    }
}
