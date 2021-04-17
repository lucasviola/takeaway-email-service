<?php

namespace Tests\Client;

use App\Client\MailjetClient;
use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MailjetClientTest extends TestCase
{

    public function testShouldPostToToMailjetApi()
    {
        $message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $httpClient = $this->getMockBuilder(Client::class)->getMock();
        $httpClient->expects($spy = $this->any())
            ->method('post');
        $mapper = new MessageMapper();
        $sut = new MailjetClient($httpClient, $mapper); //TODO: improve interface

        $sut->postMessageToMailjetApi($message);

        $this->assertEquals(1, $spy->getInvocationCount());
    }
}
