<?php

namespace App\Services;

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Swift_Mailer;
use Swift_Message;

class EmailService extends AMQPService
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setQueueName('email');
    }

    /**
     * @param $messageJsonBody
     * @return void
     */
    public function AMQPSendEmail( $messageJsonBody )
    {
        try {
            $channel = $this->getConnection()->channel();
            $channel->queue_declare($this->getQueueName(), false, true, false, false);
            $channel->exchange_declare($this->getExchange(), AMQPExchangeType::DIRECT, false, true, false);

            $channel->queue_bind($this->getQueueName(), $this->getExchange());

            $message = new AMQPMessage(
                json_encode($messageJsonBody),
                [ 'content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT ]
            );

            $channel->basic_publish($message, $this->getExchange());

            $channel->close();
            $this->getConnection()->close();

        } catch (\Exception $ex ) {
            // do some log staff
        }

    }

    /**
     * @return void
     */
    public function AMQPConsumer()
    {
        try {
            $consumerTag = 'slim';
            $channel = $this->getConnection()->channel();

            $channel->queue_declare($this->getQueueName(), false, true, false, false);
            $channel->exchange_declare($this->getExchange(), AMQPExchangeType::DIRECT, false, true, false);
            $channel->queue_bind($this->getQueueName(), $this->getExchange());

            $channel->basic_consume($this->getQueueName(),
                $consumerTag,
                false,
                false,
                false,
                false,
                [ $this, 'processMessage' ]
            );

            register_shutdown_function([ $this, 'shutdown' ], $channel, $this->getConnection());

            // Loop as long as the channel has callbacks registered
            $channel->consume();

        } catch (\Exception $ex ) {
            // some log staff
        }
    }

    /**
     * @param AMQPMessage $message
     *
     * @return void
     */
    public function processMessage( AMQPMessage $message )
    {
        $mailer = app()->getContainer()->get(Swift_Mailer::class);
        $message_data = json_decode($message->body, true);

        // Then we can just call the Mailer from any Controller method.
        $swift_message = (new Swift_Message('Hello from PHP Challenge'))
            ->setFrom([ "{$message_data['recipient']["toEmail"]}" => 'PHP Challenge'])
            ->setTo([ $message_data['recipient']["toEmail"] ])
            ->setBody($message_data['body']);

        // Later just do the actual email sending.
        $response = $mailer->send($swift_message);

        $message->ack();
    }

}