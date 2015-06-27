<?php
$container = new \King23\DI\DependencyContainer();

// the output writer
$container->register(
    \Knight23\Core\Output\WriterInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\Knight23\Core\Output\ColoredTextWriter::class);
    }
);

// the default theme
$container->register(
    \Knight23\Core\Colors\SimpleReplaceThemeInterface::class,
    function () {
        return new \Knight23\Core\Colors\SimpleReplaceTheme();
    }
);


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

// register a banner class - allows easy override for own banners
$container->register(
    \Knight23\Core\Banner\BannerInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\Castle23\Banner::class);
    }
);


// register the main application itself
$container->register(
    \Knight23\Core\RunnerInterface::class,
    function () use ($container) {
        // instance for the class
        return $container->getInstanceOf(\Knight23\Core\Knight23::class);
    }
);


// register a router service
$container->register(
    \King23\Http\RouterInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\King23\Http\Router::class);
    }
);


$container->register(
    \King23\Http\MiddlewareQueueInterface::class,
    function () use ($container) {
        /** @var \King23\Http\MiddlewareQueue $queue */
        $queue = $container->getInstanceOf(\King23\Http\MiddlewareQueue::class);
        $queue->addMiddleware(\King23\Http\Middleware\Whoops\Whoops::class);
        $queue->addMiddleware(\King23\Http\RouterInterface::class);
        return $queue;
    }
);

$container->register(
    \Psr\Log\LoggerInterface::class,
    function () {
        return new \Devedge\Log\NoLog();
    }
);


return $container;
