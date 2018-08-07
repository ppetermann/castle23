<?php
namespace Castle23\Example;

use King23\Controller\Controller;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\Promise\Deferred;

class ExampleView extends Controller
{

    /**
     * @var LoggerInterface
     */
    private $log;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @param LoggerInterface $log
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(LoggerInterface $log, ResponseFactoryInterface $responseFactory)
    {
        $this->log = $log;
        $this->responseFactory = $responseFactory;
    }


    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->log;
    }

    public function index(ServerRequestInterface $request)
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('hello world');

        return $response;
    }
}