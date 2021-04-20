<?php

namespace Tests\Unit;

use App\Exceptions\MailjetNotAvailableException;
use App\Mapper\MessageMapper;
use App\Client\MailjetEmailClient;
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
        $this->message = new Message($messageId, new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json'],
                $this->buildMailjetResponseBody()),
        ]);
        $handlerStack = HandlerStack::create($client);
        $this->httpClient = new Client(['handler' => $handlerStack]);
        $this->mailjetEmailClient = new MailjetEmailClient($this->mapper, $this->httpClient);
    }

    public function testWhenApiReturnsOkShouldMapResponseToDomainMessage() {
        $expectedResponse = ['messageId' => '1152921511742440156', 'status' => 'success'];

        $actualResponse = $this->mailjetEmailClient->postMessage($this->message);

        $this->assertEquals($actualResponse, $expectedResponse);
    }

    public function testShouldBuildRequestOptionsFromMessage() {
        $expectedRequestOptions = [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($this->mapper->mapMessageToMailjetMessage($this->message)),
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

    private function buildMailjetResponseBody(): string {
        return '{"Messages":[{"Status":"success","CustomID":"developmentTest","To":[{"Email":"lucasmatzenbacher@gmail.com","MessageUUID":"fa2f032e-299e-4541-9ec0-b83f86e673f2","MessageID":1152921511742440156,"MessageHref":"https://api.mailjet.com/v3/REST/message/1152921511742440156"}],"Cc":[],"Bcc":[]}]}';
    }
}
