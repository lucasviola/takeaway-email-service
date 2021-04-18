<?php

namespace App\Client;

use App\Adapter\MailjetEmailClient;
use App\Model\Message;
use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;

class PostEmailService
{
    private MailjetEmailClient $client;

    public function __construct(MailjetEmailClient $mailjetClient)
    {
        $this->client = $mailjetClient;
    }

    public function post(Message $message): StreamInterface
    {
        return $this->client->postMessage($message);
    }
}
