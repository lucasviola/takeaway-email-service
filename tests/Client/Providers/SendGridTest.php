<?php

namespace Tests\Client\Providers;

use App\Client\Providers\Mailjet;
use App\Client\Providers\SendGrid;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Utils\JSONParser;
use PHPUnit\Framework\TestCase;

class SendGridTest extends TestCase
{
    public function testShouldBuildRequestOptionsWithAuthorizationBearerAndMessagePayload() {
        $mapper = new MessageMapper();
        $sendGridRequestBody = $mapper->mapMessageToSendgridMessage($this->buildMessage(uniqid()));
        $expectedRequestOptions = [
            'headers'  => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('SENDGRID_API_KEY')
            ],
            'body' => JSONParser::parseToString($sendGridRequestBody),
            'debug' => false
        ];

        $actualRequestOptions = SendGrid::buildRequestOptions($sendGridRequestBody);

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
