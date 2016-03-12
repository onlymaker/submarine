<?php
/**
 * Created by PhpStorm.
 * User: Syncxplus
 * Date: 2014/12/2
 * Time: 9:12
 */

namespace utils;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitHandler {
    protected $connection;
    protected $channel;
    protected $exchange;
    protected $routeKey;
    function __construct($host, $port, $user, $pwd, $exchange, $routeKey) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pwd);
        $this->exchange = $exchange;
        $this->routeKey = $routeKey;
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->exchange, "topic", false, false, false);
    }

    function send($message) {
        $message = new AMQPMessage($message);
        $this->channel->basic_publish($message, $this->exchange, $this->routeKey);
    }

    function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
} 