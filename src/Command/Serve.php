<?php
namespace Castle23\Command;

use Castle23\Http\Server;
use King23\DI\ContainerInterface;
use Knight23\Core\Banner\BannerInterface;
use Knight23\Core\Command\BaseCommand;
use Knight23\Core\Output\WriterInterface;
use Knight23\Core\RunnerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface;

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
     * @var ServerInterface
     */
    private $socket;
    /**
     * @var Server
     */
    private $httpServer;

    /**
     * @param WriterInterface $output
     * @param BannerInterface $banner
     * @param RunnerInterface $runner
     * @param ContainerInterface $container
     * @param LoopInterface $loop
     * @param Server $httpServer
     */
    public function __construct(
        WriterInterface $output,
        BannerInterface $banner,
        RunnerInterface $runner,
        ContainerInterface $container,
        LoopInterface $loop,
        Server $httpServer
    ) {
        $this->setName("serve");
        $this->setShort("serve");

        $this->output = $output;
        $this->banner = $banner;
        $this->runner = $runner;
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
     * @throws \King23\DI\Exception\NotFoundException
     */
    public function run(array $options, array $arguments)
    {
        $port = isset($arguments[0]) ? $arguments[0] : "8001";
        $host = isset($arguments[1]) ? $arguments[1] : "0.0.0.0";

        $uri = "$host:$port";

        $server = new \React\Socket\Server($uri, $this->loop);
        $this->httpServer->setup($server);

        $this->loop->run();
    }
}
