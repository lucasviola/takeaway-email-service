<?php

namespace Tests\Unit;

use App\Client\MailjetEmailClient;
use App\Client\SendGridEmailClient;
use App\Mapper\MessageMapper;
use App\Model\Message;
use App\Model\MessageSent;
use App\Model\MessageStatus;
use App\Service\PostEmailService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class PostEmailServiceTest extends TestCase
{
    public function testShouldUseSendGridAsFallBackWhenMailServiceIsNotAvailable() {
        $messageId = uniqid();
        $message = $this->buildMessage($messageId);
        $badMailjetClient = $this->mockMailjetClient();
        $goodSendGridClient = $this->mockSendGridClient();
        $postEmailService = new PostEmailService($badMailjetClient, $goodSendGridClient, new MessageMapper());
        $expectedResponse = new MessageSent(['messageId' => $messageId, 'status' => MessageStatus::SENT]);

        $response = $postEmailService->post($message);

        $this->assertEquals($response->getAttributes()['status'], $expectedResponse->getAttributes()['status']);
        $this->assertNotNull($expectedResponse->getAttributes()['messageId']);
    }
    private function mockSendGridClient() {
        $mapper = new MessageMapper();
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json'], '[]'),
        ]);
        $handlerStack = HandlerStack::create($client);
        $mockHttpClient = new Client(['handler' => $handlerStack]);
        return new SendGridEmailClient($mapper, $mockHttpClient);
    }

    private function mockMailjetClient() {
        $mapper = new MessageMapper();
        $client = new MockHandler([
            new RequestException('Error Communicating with Server',
                new Request('POST', 'test'))
        ]);
        $handlerStack = HandlerStack::create($client);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        return new MailjetEmailClient($mapper, $this->httpClient);
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
