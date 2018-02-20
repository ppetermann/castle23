<?php
namespace Castle23\Example;

use King23\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class ExampleView extends Controller
{

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @param LoggerInterface $log
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }


    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->log;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response->withStatus(200)
            ->getBody()->write('hello world');

        return $response;
    }
}