<?php

namespace App\Client;

use GuzzleHttp\Client;

class MailjetClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function callSendMessage($message) {
        $options = [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($this->buildBodyRequestFrom($message)),
            'debug' => false
        ];

        $response = $this->client->post(env('MAILJET_MESSAGE_URL'), $options);

        return $response->getBody();
    }

    private function buildBodyRequestFrom($message) {
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
