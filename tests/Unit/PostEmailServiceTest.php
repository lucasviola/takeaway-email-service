<?php

namespace Tests\Unit;

use App\Adapter\MailjetEmailClient;
use App\Client\PostEmailService;
use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class PostEmailServiceTest extends TestCase
{

    public function testShouldPostMessageUsingMailjetAdapter()
    {
        $message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $httpClient = $this->getMockBuilder(Client::class)->getMock();
        $httpClient->expects($spy = $this->any())
            ->method('post');
        $sut = new PostEmailService($httpClient, new MailjetEmailClient(new MessageMapper())); //TODO: improve interface

        $sut->post($message);

        $this->assertEquals(1, $spy->getInvocationCount());
    }
}
