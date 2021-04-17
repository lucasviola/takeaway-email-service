<?php

namespace App\Client;

use App\Mapper\MessageMapper;
use GuzzleHttp\Client;

class MailjetClient
{
    private $client;
    private $mapper;

    public function __construct(Client $client, MessageMapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    public function callSendMessage($message) {
        $options = [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($this->mapper->mapToMailjetMessage($message)),
            'debug' => false
        ];

        $response = $this->client->post(env('MAILJET_MESSAGE_URL'), $options);

        return $response->getBody();
    }

    public function buildBodyRequestFrom($message) {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $message->getFrom()->getEmail(),
                        'Name' => $message->getFrom()->getName()
                    ],
                    'To' => [
                        [
                            'Email' => $message->getTo()->getEmail(),
                            'Name' => $message->getTo()->getName()
                        ]
                    ],
                    'Subject' => $message->getSubject(),
                    'TextPart' => $message->getMessage(),
                    'CustomID' => "developmentTest"
                ]
            ]
        ];
        return $body;
    }
}
