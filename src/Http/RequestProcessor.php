<?php
namespace Castle23\Http;

use Evenement\EventEmitter;
use King23\Http\MiddlewareQueueInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Socket\ConnectionInterface;

class RequestProcessor extends EventEmitter
{
    protected $closed = false;

    /** @var int */
    private $maxSize = 4096;
    /** @var string */
    private $buffer = '';
    /**
     * @var ConnectionInterface
     */
    private $connection;
    /**
     * @var MiddlewareQueueInterface
     */
    private $queue;

    /**
     * @param string $data
     */
    public function parseRequest($data)
    {
        if (strlen($this->buffer) + strlen($data) > $this->maxSize) {
            $this->emit('error', [new \OverflowException('Maximum header size 3exceeded')]);
        }

        $this->buffer .= $data;

        if (false !== strpos($this->buffer, "\r\n\r\n")) {
            //$request = \GuzzleHttp\Psr7\parse_request($this->buffer);

            $data = $this->parseMessage($this->buffer);

            if (!preg_match('/^[a-zA-Z]+\s+\/.*/', $data['start-line'])) {
                throw new \InvalidArgumentException('Invalid request string');
            }
            $parts = explode(' ', $data['start-line'], 3);
            //$version = isset($parts[2]) ? explode('/', $parts[2])[1] : '1.1';

            // make a copy of $_SERVER
            $server = $_SERVER;

            // files array
            // no support for uploaded files YET, so empty array
            $files = [];

            // get uri
            $uri = $this->parseRequestUri($parts[1], $data['headers']);

            // get method
            $method = $parts[0];

            $request = new \Zend\Diactoros\ServerRequest(
                $server,
                $files, // no support for uploaded files YET,
                $uri,
                $method,
                new BodyStream($data['body']),
                $data['headers']
            );

            $response = new \Zend\Diactoros\Response(new BodyStream(""));

            $this->emit('request', [$request, $response]);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function runRequest(ServerRequestInterface $request, ResponseInterface $response)
    {

        $queue = clone $this->queue;
        $response = $queue->run($request, $response);

        $reasonPhrase = $response->getReasonPhrase();
        $reasonPhrase = ($reasonPhrase ? ' '.$reasonPhrase : '');

        $this->connection->write(
            sprintf("HTTP/%s %d%s\r\n", $response->getProtocolVersion(), $response->getStatusCode(), $reasonPhrase)
        );

        // additional headers
        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                $this->connection->write(
                    sprintf("%s: %s\r\n", $header, $value)
                );
            }
        }
        $this->connection->write("\r\n");

        $this->connection->end((string) $response->getBody());
        $this->emit('end');
    }

    /**
     * Parses an HTTP message into an associative array.
     *
     * The array contains the "start-line" key containing the start line of
     * the message, "headers" key containing an associative array of header
     * array values, and a "body" key containing the body of the message.
     *
     * this method has been taken from GuzzleHttp\Psr7, which includes this
     * as a "private" function under MIT
     *
     * @link https://github.com/guzzle/psr7
     *
     * @param string $message HTTP request or response to parse.
     *
     * @return array
     * @internal
     */
    protected function parseMessage($message)
    {
        // Iterate over each line in the message, accounting for line endings
        $lines = preg_split('/(\\r?\\n)/', $message, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = ['start-line' => array_shift($lines), 'headers' => [], 'body' => ''];
        array_shift($lines);

        for ($i = 0, $totalLines = count($lines); $i < $totalLines; $i += 2) {
            $line = $lines[$i];
            // If two line breaks were encountered, then this is the end of body
            if (empty($line)) {
                if ($i < $totalLines - 1) {
                    $result['body'] = implode('', array_slice($lines, $i + 2));
                }
                break;
            }
            if (strpos($line, ':')) {
                $parts = explode(':', $line, 2);
                $key = trim($parts[0]);
                $value = isset($parts[1]) ? trim($parts[1]) : '';
                $result['headers'][$key][] = $value;
            }
        }

        return $result;
    }

    /**
     * Constructs a URI for an HTTP request message.
     *
     * @param string $path Path from the start-line
     * @param array $headers Array of headers (each value an array).
     *
     * this method has been taken from GuzzleHttp\Psr7, which includes this
     * as a "private" function under MIT
     * @link https://github.com/guzzle/psr7
     *
     * @return string
     * @internal
     */
    protected function parseRequestUri($path, array $headers)
    {
        $hostKey = array_filter(
            array_keys($headers),
            function ($k) {
                return strtolower($k) === 'host';
            }
        );
        // If no host is found, then a full URI cannot be constructed.
        if (!$hostKey) {
            return $path;
        }
        $host = $headers[reset($hostKey)][0];
        $scheme = substr($host, -4) === ':443' ? 'https' : 'http';

        return $scheme.'://'.$host.'/'.ltrim($path, '/');
    }

    /**
     * @param ConnectionInterface $connection
     * @param MiddlewareQueueInterface $queue
     */
    public function __construct(ConnectionInterface $connection, MiddlewareQueueInterface $queue)
    {
        $this->connection = $connection;
        $this->queue = $queue;

        $this->connection->on('end', function () {
            $this->close();
        });
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }
        $this->closed = true;
        $this->emit('close');
        $this->removeAllListeners();
        $this->connection->close();
    }
}
