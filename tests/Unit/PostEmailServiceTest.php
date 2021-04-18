<?php

namespace Tests\Service;

use App\Client\MailjetEmailClient;
use App\Client\SendGridEmailClient;
use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use App\Service\PostEmailService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PostEmailServiceTest extends TestCase
{
    public function testShouldUseSendGridAsFallBackWhenMailServiceIsNotAvailable() {
        $message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $badMailjetClient = $this->mockMailjetClient();
        $goodSendGridClient = $this->mockSendGridClient();
        $postEmailService = new PostEmailService($badMailjetClient, $goodSendGridClient);
        $expectedSendgridResponse = ['messageId' => 'sendgrid', 'status' => 'success'];

        $response = $postEmailService->post($message);

        $this->assertEquals($response, $expectedSendgridResponse);
    }
    private function mockSendGridClient() {
        $mapper = new MessageMapper();
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json']),
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
}
