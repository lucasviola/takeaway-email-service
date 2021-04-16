<?php

namespace App\Client;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetClient
{
    public function callSendMessage($message) {
        $mailjetClient = new Client(
            env('MAILJET_PUBLIC_KEY'),
            env('MAILJET_PRIVATE_KEY'),
            true,['version' => 'v3.1']);

        $response = $mailjetClient->post(Resources::$Email, ['body' => $this->buildBodyRequestFrom($message)]);

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
