<?php
namespace Castle23;

use King23\View\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class TestView extends View
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

    public function index($params, ServerRequestInterface $request, ResponseInterface $response)
    {
        $response->withStatus(200)
            ->getBody()->write('hello world');

        return $response;
    }
}