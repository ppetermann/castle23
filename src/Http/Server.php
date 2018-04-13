<?php
namespace Castle23\Http;

use King23\Http\MiddlewareQueueInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ServerInterface;

class Server
{
    /**
     * @var MiddlewareQueueInterface
     */
    private $queue;

    /**
     * @param ServerInterface $socketServer
     * @param MiddlewareQueueInterface $queue
     */
    public function __construct(MiddlewareQueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function setup(ServerInterface $socketServer)
    {
        $socketServer->on('connection', [$this, 'connection']);
    }

    public function connection(ConnectionInterface $connection)
    {
        $requestEmitter = new RequestProcessor($connection, $this->queue);
        $connection->on('data', [$requestEmitter, 'parseRequest']);
        $requestEmitter->on('request', [$requestEmitter, 'runRequest']);
    }

}
