<?php


namespace App\Consumer;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumer
{
    public function consumeMessage(AMQPMessage $message)
    {
        $exchange = env('RABBITMQ_EXCHANGE_NAME');
        $queue = env('RABBITMQ_QUEUE_NAME');
        $consumerTag = 'consumer';

        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'),
            env('RABBITMQ_VHOST'));
        $channel = $connection->channel();

        list($queueName, ,) = $channel->queue_declare($queue, false, false, true, true);
        $channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, false, true);

        $channel->queue_bind($queueName, $exchange);

        $channel->basic_consume($queueName, $consumerTag, false, false, true, false, 'process_message');

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
