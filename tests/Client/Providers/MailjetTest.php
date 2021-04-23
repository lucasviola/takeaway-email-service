<?php

namespace Tests\Client\Providers;

use App\Client\Providers\Mailjet;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Utils\JSONParser;
use PHPUnit\Framework\TestCase;

class MailjetTest extends TestCase
{


    public function testShouldBuildRequestOptionsFromMessage() {
        $mapper = new MessageMapper();
        $mailjetRequestBody = $mapper->mapMessageToMailjetMessage($this->buildMessage(uniqid()));
        $expectedRequestOptions = [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => JSONParser::parseToString($mailjetRequestBody),
            'debug' => false
        ];

        $actualRequestOptions = Mailjet::buildRequestOptions($mailjetRequestBody);

        $this->assertEquals($actualRequestOptions, $expectedRequestOptions);
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
