<?php

namespace Tests\Unit;

use App\Mapper\MessageMapper;
use App\Model\Message;
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
                          "subject": "subject",
                          "message": "message"
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
                        'Email' => $message->getAttributes()['from']['email'],
                        'Name' =>  $message->getAttributes()['from']['name']
                    ],
                    'To' => [
                        [
                            'Email' => $message->getAttributes()['to']['email'],
                            'Name' =>  $message->getAttributes()['to']['name']
                        ]
                    ],
                    'Subject' => $message->getAttributes()['subject'],
                    'TextPart' => $message->getAttributes()['message'],
                    'CustomID' => $message->getAttributes()['messageId']
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
                            'email' => $message->getAttributes()['to']['email'],
                        ],
                    ],
                ],
            ],
            'from' => [
                'email' => $message->getAttributes()['from']['email'],
            ],
            'subject' => $message->getAttributes()['subject'],
            'content' => [
                0 => [
                    'type' => 'text/plain',
                    'value' => $message->getAttributes()['message'],
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
                'name' => $message->getAttributes()['from']['name'],
                'email' => $message->getAttributes()['from']['email'],
            ],
            'to' => [
                'name' => $message->getAttributes()['to']['name'],
                'email' => $message->getAttributes()['to']['email'],
            ],
            'subject' => $message->getAttributes()['subject'],
            'message' => $message->getAttributes()['message'],
        ];

        $actual =  $mapper->mapMessageToJson($message);

        $this->assertEquals($actual, $expected);
    }

    public function testShouldMapFromMessageToMessageEntity() {
        $mapper = new MessageMapper();
        $messageId = uniqid();
        $message = $this->buildMessage($messageId);
        $expected = [
            'from' => $message->getAttributes()['from']['email'],
            'messageId' => $message->getAttributes()['messageId'],
            'to' => $message->getAttributes()['to']['email'],
            'subject' => $message->getAttributes()['subject'],
            'message' => $message->getAttributes()['message']
        ];

        $actual =  $mapper->mapMessageToMessageEntity($message);

        $this->assertEquals($actual, $expected);
    }

    private function buildMailjetResponseBody(): string {
        return '{"Messages":[{"Status":"success","CustomID":"developmentTest","To":[{"Email":"lucasmatzenbacher@gmail.com","MessageUUID":"fa2f032e-299e-4541-9ec0-b83f86e673f2","MessageID":1152921511742440156,"MessageHref":"https://api.mailjet.com/v3/REST/message/1152921511742440156"}],"Cc":[],"Bcc":[]}]}';
    }

    private function buildMessage($messageId): Message
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
        return new Message($attributes);
    }
}
