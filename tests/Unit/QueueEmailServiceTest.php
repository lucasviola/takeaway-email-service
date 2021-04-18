<?php

namespace Tests\Unit;

use App\Client\RabbitMQClient;
use App\Mapper\MessageMapper;
use App\Model\From;
use App\Model\Message;
use App\Model\To;
use App\Service\QueueEmailService;
use PHPUnit\Framework\TestCase;

class QueueEmailServiceTest extends TestCase
{

    public function testShouldMapMessageToStringBeforeSendingIt()
    {
        $message = new Message(new From('name', 'email'),
            new To('name', 'email'), 'Test', 'Test');
        $expectedMessageAsString = $this->buildMessageRequest();
        $mapper = new MessageMapper();
        $client = $this->getMockBuilder(RabbitMQClient::class)->getMock();
        $queueEmailService = new QueueEmailService($client, $mapper);

        $client->expects($this->exactly(1))
            ->method('produceToRabbitMq')
            ->with($expectedMessageAsString);

        $queueEmailService->publish($message);
    }

    private function buildMessageRequest() {
        return '{"from":{"name":"name","email":"email"},"to":{"name":"name","email":"email"},"subject":"Test","message":"Test"}';
    }
}
