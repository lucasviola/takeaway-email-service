<?php

namespace Tests\Unit;

use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use PHPUnit\Framework\TestCase;

class MessageMapperTest extends TestCase
{
    private string $messageRequest;

    protected function setUp(): void
    {
        $this->messageRequest = '{
                          "from": {
                            "name": "name",
                            "email": "email"
                          },
                          "to": {
                            "name": "name",
                            "email": "email"
                          },
                          "subject": "Test",
                          "message": "Test"
                        }';
    }

    public function testShouldTransformMessageDomainIntoMailjetMessage() {
        $messageMapper = new MessageMapper();
        $messageId = uniqid();
        $message = new Message($messageId, new From('name', 'email'),
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
                    'CustomID' => $message->getMessageId()
                ]
            ]
        ];

        $actualMailjetMessage = $messageMapper->mapMessageToMailjetMessage($message);

        $this->assertEquals($expectedMailjetMessage, $actualMailjetMessage);
    }

    public function testShouldTransformRequestBodyIntoMessageDomainModel() {
        $messageId = uniqid();
        $expectedMessage = new Message($messageId, new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $messageMapper = new MessageMapper();
        $requestBody = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '',
            $this->messageRequest), true);

        $actualMessage = $messageMapper->mapMessageRequestToDomainModel($requestBody, $messageId);

        $this->assertEquals($expectedMessage, $actualMessage);
    }

    public function testShouldMapFromMailjetMessageToMessageResponse() {
        $messageMapper = new MessageMapper();
        $mailjetResponseAsString = $this->buildMailjetResponseBody();
        $externalServiceResponse = json_decode($mailjetResponseAsString,true);
        $expectedMessageResponse = ['messageId' => '1152921511742440156', 'status' => 'success'];

        $actualMessageResponse = $messageMapper->mapMailjetResponseToMessageResponse($externalServiceResponse);

        $this->assertEquals($actualMessageResponse, $expectedMessageResponse);
    }

    public function testShouldMapFromMessageToSendgridMessage() {
        $messageMapper = new MessageMapper();
        $messageId = uniqid();
        $message = new Message($messageId, new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $expectedSendgridMessage = [
            'personalizations' => [
                0 => [
                    'to' => [
                        0 => [
                            'email' => $message->getTo()->getEmail(),
                        ],
                    ],
                ],
            ],
            'from' => [
                'email' => $message->getFrom()->getEmail(),
            ],
            'subject' => $message->getSubject(),
            'content' => [
                0 => [
                    'type' => 'text/plain',
                    'value' => $message->getMessage(),
                ],
            ],
        ];

        $actualSendgridMessage = $messageMapper->mapMessageToSendgridMessage($message);

        $this->assertEquals($actualSendgridMessage, $expectedSendgridMessage);
    }

    public function testShouldMapMessageToString() {
        $mapper = new MessageMapper();
        $messageId = uniqid();
        $message = new Message($messageId, new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $expected = [
            'from' => [
                'name' => $message->getFrom()->getName(),
                'email' => $message->getFrom()->getEmail(),
            ],
            'to' => [
                'name' => $message->getTo()->getName(),
                'email' => $message->getTo()->getEmail(),
            ],
            'subject' => $message->getSubject(),
            'message' => $message->getMessage(),
        ];

        $actual =  $mapper->mapMessageToJson($message);

        $this->assertEquals($actual, $expected);
    }

    public function testShouldMapFromMessageToMessageEntity() {
        $mapper = new MessageMapper();
        $messageId = uniqid();
        $message = new Message($messageId, new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $expected = [
            'from' => $message->getFrom()->getEmail(),
            'messageId' => $messageId,
            'to' => $message->getTo()->getEmail(),
            'subject' => $message->getSubject(),
            'message' => $message->getMessage()
        ];

        $actual =  $mapper->mapMessageToMessageEntity($message);

        $this->assertEquals($actual, $expected);
    }

    private function buildMailjetResponseBody(): string {
        return '{"Messages":[{"Status":"success","CustomID":"developmentTest","To":[{"Email":"lucasmatzenbacher@gmail.com","MessageUUID":"fa2f032e-299e-4541-9ec0-b83f86e673f2","MessageID":1152921511742440156,"MessageHref":"https://api.mailjet.com/v3/REST/message/1152921511742440156"}],"Cc":[],"Bcc":[]}]}';
    }
}
