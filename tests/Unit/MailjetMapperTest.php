<?php

namespace Tests\Unit;

use App\Client\GenericClient;
use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use PHPUnit\Framework\TestCase;

class MessageMapperTest extends TestCase
{

    public function testShouldTransformMessageDomainIntoMailjetMessage() {
        $messageMapper = new MessageMapper();
        $message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $expectedMailjetMessage = [
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

        $actualMailjetMessage = $messageMapper->mapToMailjetMessage($message);

        $this->assertEquals($expectedMailjetMessage, $actualMailjetMessage);
    }

    public function testShouldTransformRequestBodyIntoMessageDomainModel() {
        $to = new To('name', 'email');
        $from = new From('name', 'email');
        $expectedMessage = new Message($from, $to, 'subject', 'message');
        $messageMapper = new MessageMapper();
        $jsonAsString = '{
                          "from": {
                            "name": "name",
                            "email": "email"
                          },
                          "to": {
                            "name": "name",
                            "email": "email"
                          },
                          "subject": "subject",
                          "message": "message"
                        }';
        $requestBody = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '',
            $jsonAsString), true);

        $actualMessage = $messageMapper->mapToDomainModel($requestBody);

        $this->assertEquals($expectedMessage, $actualMessage);
    }
}
