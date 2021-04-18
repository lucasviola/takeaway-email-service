<?php

namespace Tests\Unit;

use App\Mapper\MessageMapper;
use App\Client\MailjetEmailClient;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MailjetEmailClientTest extends TestCase
{
    private MessageMapper $mapper;
    private MailjetEmailClient $mailjetAdapter;
    private Message $message;
    private Client $httpClient;


    protected function setUp(): void
    {
        $this->mapper = new MessageMapper();
        $this->message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $this->httpClient = $this->getMockBuilder(Client::class)->getMock();
        $this->mailjetAdapter = new MailjetEmailClient($this->mapper, $this->httpClient);
    }

    public function testShouldPostMessageUsingMailjetAdapter()
    {
        $this->httpClient->expects($spy = $this->any())
            ->method('post');

        $this->mailjetAdapter->postMessage($this->message);

        $this->assertEquals(1, $spy->getInvocationCount());
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
        $actualRequestOptions = $this->mailjetAdapter->buildRequestOptions($this->message);

        $this->assertEquals($actualRequestOptions, $expectedRequestOptions);
    }

}
