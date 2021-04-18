<?php

namespace App\Client;

use App\Adapter\MailjetEmailClient;
use App\Model\Message;
use GuzzleHttp\Client;

class PostEmailAdapter
{
    private $client;
    private $adapter;

    public function __construct(Client $client, MailjetEmailClient $adapter)
    {
        $this->client = $client;
        $this->adapter = $adapter;
    }

    public function post(Message $message) {

        $response = $this->client->post($this->adapter->getUrl(),
            $this->adapter->buildRequestOptions($message));

        return $response->getBody();
    }
}
