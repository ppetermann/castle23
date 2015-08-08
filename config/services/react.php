<?php

// the react event loop - this uses the factory by default - which should allow it to run on most
// php enabled systems
$container->register(
    \React\EventLoop\LoopInterface::class,
    function () {
        return \React\EventLoop\Factory::create();
    }
);

$container->register(
    \React\Socket\ServerInterface::class,
    function() use ($container) {
        /** @var \React\EventLoop\LoopInterface $loop */
        $loop = $container->getInstanceOf(\React\EventLoop\LoopInterface::class);
        return new \React\Socket\Server($loop);
    }
);
