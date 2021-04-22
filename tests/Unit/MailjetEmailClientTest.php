<?php

namespace Tests\Unit;

use App\Exceptions\MailjetNotAvailableException;
use App\Utils\JSONParser;
use App\Mapper\MessageMapper;
use App\Client\MailjetEmailClient;
use App\Model\MailjetResponse;
use App\Model\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class MailjetEmailClientTest extends TestCase
{
    private MessageMapper $mapper;
    private MailjetEmailClient $mailjetEmailClient;
    private Message $message;
    private Client $httpClient;


    protected function setUp(): void
    {
        $this->mapper = new MessageMapper();
        $messageId = uniqid();
        $this->message = $this->buildMessage($messageId);
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json'],
                $this->buildMailjetResponseStub()),
        ]);
        $handlerStack = HandlerStack::create($client);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        $this->mailjetEmailClient = new MailjetEmailClient($this->mapper, $this->httpClient);
    }

    public function testWhenApiReturnsOkShouldMapResponseToDomainMessage() {
        $expectedMailjetResponse =
            new MailjetResponse($this->buildMailjetResponse());

        $actualResponse = $this->mailjetEmailClient->postMessage($this->message);

        $this->assertEquals($actualResponse, $expectedMailjetResponse);
    }

    public function testShouldBuildRequestOptionsFromMessage() {
        $expectedRequestOptions = [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => JSONParser::parseToString($this->mapper->mapMessageToMailjetMessage($this->message)),
            'debug' => false
        ];
        $actualRequestOptions = $this->mailjetEmailClient->buildRequestOptions($this->message);

        $this->assertEquals($actualRequestOptions, $expectedRequestOptions);
    }

    public function testShouldThrowMailjetNotAvailableWhenHttpExceptionOccurs() {
        $client = new MockHandler([
            new RequestException('Error Communicating with Server',
                new Request('POST', 'test'))
        ]);
        $handlerStack = HandlerStack::create($client);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        $badMailjetClient =  new MailjetEmailClient($this->mapper, $this->httpClient);

        $this->expectException(MailjetNotAvailableException::class);

        $badMailjetClient->postMessage($this->message);
    }

    private function buildMailjetResponseStub(): string {
        return '{"Messages":[{"Status":"success","CustomID":"messageId","To":[{"Email":"test@email.com","MessageUUID":"test","MessageID":123,"MessageHref":"https://mailjet.href"}],"Cc":[],"Bcc":[]}]}';
    }

    private function buildMailjetResponse() {
        return [
            'Messages' => [
                0 => [
                    'Status' => 'success',
                    'CustomID' => 'messageId',
                    'To' => [
                        0 => [
                            'Email' => 'test@email.com',
                            'MessageUUID' => 'test',
                            'MessageID' => '123',
                            'MessageHref' => 'https://mailjet.href'
                        ],
                    ],
                    'Cc' => [
                    ],
                    'Bcc' => [
                    ],
                ],
            ]
        ];
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
