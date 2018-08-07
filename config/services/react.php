<?php /** @noinspection PhpUnhandledExceptionInspection */
/** @var \King23\DI\ContainerInterface $container */
// the react event loop - this uses the factory by default - which should allow it to run on most
// php enabled systems
$container->register(
    \React\EventLoop\LoopInterface::class,
    function () {
        return \React\EventLoop\Factory::create();
    }
);

$container->register(
    React\Http\Server::class,
    function () use ($container) {
        return new \React\Http\Server(
            function (\Psr\Http\Message\ServerRequestInterface $request) use ($container) {
                /** @var \Psr\Http\Server\RequestHandlerInterface $handler */
                $handler =$container->getInstanceOf(\Psr\Http\Server\RequestHandlerInterface::class);
                return $handler->handle($request);
            }
        );
    }
);
