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

class Run extends BaseCommand
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
     * @param WriterInterface $output
     * @param BannerInterface $banner
     * @param RunnerInterface $runner
     * @param ContainerInterface $container
     * @param ServerInterface $socket
     * @param LoopInterface $loop
     */
    public function __construct(
        WriterInterface $output,
        BannerInterface $banner,
        RunnerInterface $runner,
        ContainerInterface $container,
        ServerInterface $socket,
        LoopInterface $loop
    ) {
        $this->setName("run");
        $this->setShort("run");

        $this->output = $output;
        $this->banner = $banner;
        $this->runner = $runner;
        $this->container = $container;
        $this->socket = $socket;
        $this->loop = $loop;
    }

    /**
     * @param array $options
     * @param array $arguments
     * @return mixed
     */
    public function run(array $options, array $arguments)
    {
        /** @var \Castle23\Http\Server $server */
        $server = $this->container->getInstanceOf(Server::class);
        $this->socket->listen(8001, "0.0.0.0");
        $this->loop->run();
    }
}
