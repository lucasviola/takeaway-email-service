<?php


namespace App\Client;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQClient
{

    public function produceToRabbitMq(string $messagePayload): void {
        $connection = AMQPStreamConnection::create_connection([
            [
                'host' => env('RABBITMQ_HOST'),
                'port' => env('RABBITMQ_PORT'),
                'user' => env('RABBITMQ_USER'),
                'password' => env('RABBITMQ_PASSWORD'),
                'vhost' => env('RABBITMQ_VHOST')],
        ]);

        $exchange = env('RABBITMQ_EXCHANGE_NAME');
        $queue = env('RABBITMQ_QUEUE_NAME');

        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, $exchange);

        $messageToBePosted = new AMQPMessage($messagePayload,
            array('content_type' => 'text/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($messageToBePosted, $exchange);

        $channel->close();
        $connection->close();
    }
}
