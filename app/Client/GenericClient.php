<?php

namespace App\Client;

use App\Adapter\MailjetAdapter;
use App\Model\Message;
use GuzzleHttp\Client;

class GenericClient
{
    private $client;
    private $adapter;

    public function __construct(Client $client, MailjetAdapter $adapter)
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
