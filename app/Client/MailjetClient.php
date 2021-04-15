<?php

namespace App\Client;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetClient
{
    public function callSendMessage() {
        $mailjetClient = new Client(
            '8308777a9b41fad649f5f640ab630d15',
            '694a4b5c97361f9d8346ae26e2df2a11',true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "lucasmatzenbacher@gmail.com",
                        'Name' => "Lucas"
                    ],
                    'To' => [
                        [
                            'Email' => "lucasmatzenbacher@gmail.com",
                            'Name' => "Lucas"
                        ]
                    ],
                    'Subject' => "Hello, from Takeaway Email Service",
                    'TextPart' => "My first Mailjet email",
                    'CustomID' => "developmentTest"
                ]
            ]
        ];
        $response = $mailjetClient->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());

        return $response;
    }
}
