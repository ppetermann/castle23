<?php
namespace Castle23\Command;

use King23\DI\ContainerInterface;
use Knight23\Core\Banner\BannerInterface;
use Knight23\Core\Command\BaseCommand;
use Knight23\Core\Output\WriterInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server;


class Serve extends BaseCommand
{
    /**
     * @var LoopInterface
     */
    private $loop;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Server
     */
    private $httpServer;

    /**
     * @var BannerInterface
     */
    private $banner;

    /**
     * @var WriterInterface
     */
    private $output;


    /**
     * @param WriterInterface $output
     * @param BannerInterface $banner
     * @param ContainerInterface $container
     * @param LoopInterface $loop
     * @param Server $httpServer
     */
    public function __construct(
        WriterInterface $output,
        BannerInterface $banner,
        ContainerInterface $container,
        LoopInterface $loop,
        Server $httpServer
    ) {
        $this->setName("serve");
        $this->setShort("serve");

        $this->output = $output;
        $this->banner = $banner;
        $this->container = $container;
        $this->loop = $loop;

        $this->addArgument('port', '8001', 'the port where the webserver is supposed to answer');
        $this->addArgument('host', '0.0.0.0', 'the host to which this webserver binds');
        $this->httpServer = $httpServer;
    }

    /**
     * @param array $options
     * @param array $arguments
     * @return mixed
     */
    public function run(array $options, array $arguments)
    {
        $port = isset($arguments[0]) ? $arguments[0] : "8001";
        $host = isset($arguments[1]) ? $arguments[1] : "0.0.0.0";

        $uri = "$host:$port";

        $socket = new \React\Socket\Server($uri, $this->loop);
        $this->httpServer->listen($socket);
        $this->loop->run();
        return 0;
    }
}
