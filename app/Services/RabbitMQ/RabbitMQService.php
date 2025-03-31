<?php

namespace App\Services\RabbitMQ;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private AMQPStreamConnection $connection;

    private AMQPChannel $channel;

    private string $queue;

    private string $exchange;

    public function __construct()
    {
        $this->queue = config('rabbitmq.queue');
        $this->exchange = config('rabbitmq.exchange');

        $this->connect();
    }

    private function connect(): void
    {
        try {
            $this->connection = new AMQPStreamConnection(
                config('rabbitmq.host'),
                config('rabbitmq.port'),
                config('rabbitmq.user'),
                config('rabbitmq.password'),
                config('rabbitmq.vhost')
            );

            $this->channel = $this->connection->channel();
            $this->setupQueue();
        } catch (\Exception $e) {
            Log::error('Failed to connect to RabbitMQ: ' . $e->getMessage());
            throw $e;
        }
    }

    private function setupQueue(): void
    {
        $this->channel->exchange_declare(
            $this->exchange,
            'fanout',
            false,
            true,
            false
        );

        $this->channel->queue_declare(
            $this->queue,
            false,
            true,
            false,
            false
        );

        $this->channel->queue_bind(
            $this->queue,
            $this->exchange
        );
    }

    public function publish(array $data): void
    {
        try {
            $message = new AMQPMessage(
                json_encode($data),
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            );

            $this->channel->basic_publish(
                $message,
                $this->exchange
            );
        } catch (\Exception $e) {
            Log::error('Failed to publish message to RabbitMQ: ' . $e->getMessage());
            throw $e;
        }
    }

    public function consume(callable $callback): void
    {
        try {
            $this->channel->basic_qos(null, 1, null);
            $this->channel->basic_consume(
                $this->queue,
                '',
                false,
                false,
                false,
                false,
                $callback
            );

            while ($this->channel->is_consuming()) {
                $this->channel->wait();
            }
        } catch (\Exception $e) {
            Log::error('Failed to consume message from RabbitMQ: ' . $e->getMessage());
            throw $e;
        }
    }

    public function close(): void
    {
        try {
            $this->channel->close();
            $this->connection->close();
        } catch (\Exception $e) {
            Log::error('Failed to close RabbitMQ connection: ' . $e->getMessage());
            throw $e;
        }
    }
}
