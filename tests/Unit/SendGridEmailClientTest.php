<?php

namespace Tests\Unit;

use App\Client\SendGridEmailClient;
use App\Exceptions\SendGridNotAvailableException;
use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SendGridEmailClientTest extends TestCase
{
    private MessageMapper $mapper;
    private Message $message;
    private SendGridEmailClient $sendGridEmailClient;

    protected function setUp(): void
    {
        $this->mapper = new MessageMapper();
        $this->message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json']),
        ]);
        $handlerStack = HandlerStack::create($client);
        $mockHttpClient = new Client(['handler' => $handlerStack]);
        $this->sendGridEmailClient = new SendGridEmailClient($this->mapper, $mockHttpClient);
    }

    public function testWhenApiReturnsOkShouldMapResponseToDomainMessage() {
        $expectedResponse = ['messageId' => 'sendgrid', 'status' => 'success'];

        $actualResponse = $this->sendGridEmailClient->postMessage($this->message);

        $this->assertEquals($actualResponse, $expectedResponse);
    }

    public function testShouldBuildRequestOptionsWithAuthorizationBearerAndMessagePayload() {
        $expectedRequestOptions = [
            'headers'  => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('SENDGRID_API_KEY')
            ],
            'body' => json_encode($this->mapper->mapMessageToSendgridMessage($this->message)),
            'debug' => false
        ];

        $actualRequestOptions = $this->sendGridEmailClient->buildRequestOptions($this->message);

        $this->assertEquals($actualRequestOptions, $expectedRequestOptions);
    }


    public function testShouldThrowSendGridNotAvailableWhenHttpExceptionOccurs() {
        $client = new MockHandler([
            new RequestException('Error Communicating with Server',
                new Request('POST', 'test'))
        ]);
        $handlerStack = HandlerStack::create($client);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        $badSendGridClient =  new SendGridEmailClient($this->mapper, $this->httpClient);

        $this->expectException(SendGridNotAvailableException::class);

        $badSendGridClient->postMessage($this->message);
    }
}
