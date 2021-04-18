<?php

namespace Tests\Unit;

use App\Client\PostEmailService;
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

        $actualMailjetMessage = $messageMapper->mapMessageToMailjetMessage($message);

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

        $actualMessage = $messageMapper->mapMessageRequestToDomainModel($requestBody);

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


    private function buildMailjetResponseBody(): string {
        return '{"Messages":[{"Status":"success","CustomID":"developmentTest","To":[{"Email":"lucasmatzenbacher@gmail.com","MessageUUID":"fa2f032e-299e-4541-9ec0-b83f86e673f2","MessageID":1152921511742440156,"MessageHref":"https://api.mailjet.com/v3/REST/message/1152921511742440156"}],"Cc":[],"Bcc":[]}]}';
    }
}
