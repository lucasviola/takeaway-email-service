<?php

namespace Tests\Unit;

use App\Utils\JSONParser;
use App\Mapper\MessageMapper;
use App\MessageEntity;
use App\Model\MailjetResponse;
use App\Model\Message;
use App\Model\MessageSent;
use App\Model\MessageStatus;
use App\Model\SendGridResponse;
use PHPUnit\Framework\TestCase;

class MessageMapperTest extends TestCase
{
    private string $messageRequest;
    private MessageMapper $messageMapper;

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
        $this->messageMapper = new MessageMapper();
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
        $status = 'status';

        $actualMessage = $messageMapper->mapMessageRequestToDomainModel($requestBody, $messageId, $status);

        $this->assertEquals($expectedMessage, $actualMessage);
    }

    public function testShouldMapFromMailjetMessageToMessageResponse() {
        $messageMapper = new MessageMapper();
        $mailjetResponseAsString = $this->buildMailjetResponseBody();
        $externalServiceResponse = JSONParser::parseToJson($mailjetResponseAsString);
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

    public function testShouldMapFromMessageToMessageEntity() {
        $mapper = new MessageMapper();
        $messageId = uniqid();
        $message = $this->buildMessage($messageId);
        $messageSent = new MessageSent([
            'status' => 'success',
            'messageId' => 'test'
        ]);
        $attributes = [
            'from' => $message->getAttributes()['from']['email'],
            'messageId' => $messageSent->getAttributes()['messageId'],
            'to' => $message->getAttributes()['to']['email'],
            'subject' => $message->getAttributes()['subject'],
            'message' => $message->getAttributes()['message'],
            'status' => $messageSent->getAttributes()['status']
        ];
        $expected = new MessageEntity($attributes);

        $actual =  $mapper->mapMessageToMessageEntity($message, $messageSent);

        $this->assertEquals($actual, $expected);
    }

    public function testShouldMapFromSendGridResponseToMessageSent() {
        $sendGridResponse = new SendGridResponse([]);

        $actualMessageSent = $this->messageMapper->mapFromSendgridResponseToMessageSent($sendGridResponse);

        $this->assertEquals($actualMessageSent->getAttributes()['status'], MessageStatus::SENT);
        $this->assertNotNull($actualMessageSent->getAttributes()['messageId']);
    }

    public function testShouldMapFromMailjetResponseToMessageSent() {
        $attributes = $this->buildMailjetResponse();
        $mailjetResponse = new MailjetResponse($attributes);
        $expectedMessageSent = new MessageSent([
            'status' => 'success',
            'messageId' => 'test'
        ]);

        $actualMessageSent = $this->messageMapper->mapFromMailjetResponseToMessageSent($mailjetResponse);

        $this->assertEquals($actualMessageSent, $expectedMessageSent);
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
            'status' => 'status'
        ];
        return new Message($attributes);
    }

    private function buildMailjetResponse()
    {
        return [
            'Messages' => [
                0 => [
                    'Status' => 'success',
                    'CustomID' => 'test',
                    'To' => [
                        0 => [
                            'Email' => 'email@gmail.com',
                            'MessageUUID' => 'uid',
                            'MessageID' => 1152921511802880648,
                            'MessageHref' => 'http://mailjet.href']],
                    'Cc' => [],
                    'Bcc' => []]]];
    }
}
