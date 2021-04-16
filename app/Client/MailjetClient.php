<?php

namespace App\Client;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetClient
{
    public function callSendMessage($message) {
        $mailjetClient = new Client(
            '8308777a9b41fad649f5f640ab630d15',
            '694a4b5c97361f9d8346ae26e2df2a11',true,['version' => 'v3.1']);

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
        $response = $mailjetClient->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());

        return $message;
    }
}
