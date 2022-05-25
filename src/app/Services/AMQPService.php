<?php

namespace App\Services;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class AMQPService
{

    /**
     * @var AMQPStreamConnection
     */
    private AMQPStreamConnection $connection;

    /**
     * @var string $queue_name
     */
    private string $queue_name;

    /**
     * @var string $exchange
     */
    private string $exchange;

    /**
     * @param string $queue_name
     * @param string $exchange
     */
    public function __construct(string $queue_name = 'email', string $exchange = 'router')
    {
        $this->setConnection();
        $this->setQueueName( $queue_name );
        $this->setExchange( $exchange );
    }

    /**
     * @return AMQPStreamConnection
     */
    protected function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * @return void
     */
    protected function setConnection(): void
    {
        $this->connection = new AMQPStreamConnection(
            env('RMQ_HOST'),
            env('RMQ_PORT'),
            env('RMQ_USERNAME'),
            env('RMQ_PASSWORD'),
            env('RMQ_VHOST')
        );
    }

    /**
     * @return string
     */
    protected function getQueueName(): string
    {
        return $this->queue_name;
    }

    /**
     * @param string $queue_name
     */
    protected function setQueueName(string $queue_name): void
    {
        $this->queue_name = $queue_name;
    }

    /**
     * @return string
     */
    protected function getExchange(): string
    {
        return $this->exchange;
    }

    /**
     * @param string $exchange
     */
    protected function setExchange(string $exchange): void
    {
        $this->exchange = $exchange;
    }

    /**
     * @param AMQPChannel $channel
     * @return void
     * @throws \Exception
     */
    public function shutdown( AMQPChannel $channel )
    {
        $channel->close();
        $this->getConnection()->close();
    }

}