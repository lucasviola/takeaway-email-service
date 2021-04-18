<?php


namespace App\Service;


use App\Client\RabbitMQClient;
use App\Mapper\MessageMapper;
use App\Model\Message;

class QueueEmailService
{
    private RabbitMQClient $rabbitMQClient;
    private MessageMapper $messageMapper;

    public function __construct(RabbitMQClient $rabbitMQClient, MessageMapper $messageMapper)
    {
        $this->rabbitMQClient = $rabbitMQClient;
        $this->messageMapper = $messageMapper;
    }

    public function publish(Message $message): void
    {
        $messageAsJson = $this->messageMapper->mapMessageToJson($message);
        $messageAsString = json_encode($messageAsJson);

        $this->rabbitMQClient->produceToRabbitMq($messageAsString);
    }
}
