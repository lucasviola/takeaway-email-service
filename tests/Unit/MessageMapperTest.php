<?php

namespace Tests\Unit;

use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\MessageModel;
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
        $message = $this->buildMessage($messageId);
        $expectedMailjetMessage = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $message['from']['email'],
                        'Name' =>  $message['from']['name']
                    ],
                    'To' => [
                        [
                            'Email' => $message['to']['email'],
                            'Name' =>  $message['to']['name']
                        ]
                    ],
                    'Subject' => $message['subject'],
                    'TextPart' => $message['message'],
                    'CustomID' => $message['messageId']
                ]
            ]
        ];

        $actualMailjetMessage = $messageMapper->mapMessageToMailjetMessage($message);

        $this->assertEquals($expectedMailjetMessage, $actualMailjetMessage);
    }

    public function testShouldTransformRequestBodyIntoMessageDomainModel() {
        $messageId = uniqid();
        $expectedMessage = $this->buildMessage($messageId);
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
        $message = $this->buildMessage($messageId);
        $expectedSendgridMessage = [
            'personalizations' => [
                0 => [
                    'to' => [
                        0 => [
                            'email' => $message['to']['email'],
                        ],
                    ],
                ],
            ],
            'from' => [
                'email' => $message['from']['email'],
            ],
            'subject' => $message['subject'],
            'content' => [
                0 => [
                    'type' => 'text/plain',
                    'value' => $message['message'],
                ],
            ],
        ];

        $actualSendgridMessage = $messageMapper->mapMessageToSendgridMessage($message);

        $this->assertEquals($actualSendgridMessage, $expectedSendgridMessage);
    }

    public function testShouldMapMessageToString() {
        $mapper = new MessageMapper();
        $messageId = uniqid();
        $message = $this->buildMessage($messageId);
        $expected = [
            'from' => [
                'name' => $message['from']['name'],
                'email' => $message['from']['email'],
            ],
            'to' => [
                'name' => $message['to']['name'],
                'email' => $message['to']['email'],
            ],
            'subject' => $message['subject'],
            'message' => $message['message'],
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

    private function buildMessage($messageId): MessageModel
    {
        $attributes = [
            'messageId' => $messageId,
            'from' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'subject' => 'subject',
            'message' => 'message',
        ];
        return new MessageModel($attributes);
    }
}
