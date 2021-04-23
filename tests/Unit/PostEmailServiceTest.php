<?php

namespace Tests\Unit;

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
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PostEmailServiceTest extends TestCase
{
    protected function setUp(): void
    {
        Log::spy();
    }

    public function testShouldUseSendGridAsFallBackWhenMailServiceIsNotAvailable() {
        $messageId = uniqid();
        $message = $this->buildMessage($messageId);
        $badMailjetClient = $this->mockMailjetClient();
        $goodSendGridClient = $this->mockSendGridClient();
        $postEmailService = new PostEmailService($badMailjetClient, new MessageMapper());
        $expectedResponse = new MessageSent(['messageId' => $messageId, 'status' => MessageStatus::SENT]);

        $response = $postEmailService->post($message);

        $this->assertEquals($response->getAttributes()['status'], $expectedResponse->getAttributes()['status']);
        $this->assertNotNull($expectedResponse->getAttributes()['messageId']);
    }
    private function mockSendGridClient() {
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json'], '[]'),
        ]);
        $handlerStack = HandlerStack::create($client);
        $mockHttpClient = new Client(['handler' => $handlerStack]);
        return new \App\Client\Client($mockHttpClient);
    }

    private function mockMailjetClient() {
        $client = new MockHandler([
            new RequestException('Error Communicating with Server',
                new Request('POST', 'test')),
            new Response(200, [], '{}'),
        ]);
        $handlerStack = HandlerStack::create($client);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        return new \App\Client\Client($this->httpClient);
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
