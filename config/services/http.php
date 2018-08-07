<?php /** @noinspection PhpUnhandledExceptionInspection */

/** @var \King23\DI\ContainerInterface $container */


$container->register(
    \Psr\Http\Message\ResponseFactoryInterface::class,
    function () {
        return new class implements  \Psr\Http\Message\ResponseFactoryInterface
        {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): \Psr\Http\Message\ResponseInterface
            {
                return new \React\Http\Response($code, [], null, '1.1', $reasonPhrase);
            }
        };
    }
);

// register the router service
$container->register(
    \King23\Http\RouterInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\King23\Http\Router::class);
    }
);

$container->register(
    \King23\Http\MiddlewareQueueInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\King23\Http\MiddlewareQueue::class);
    }
);

$container->registerFactory(
    \Psr\Http\Server\RequestHandlerInterface::class,
    function () use ($container) {
        /** @var \King23\Http\MiddlewareQueue $queue */
        $queue = $container->getInstanceOf(\King23\Http\MiddlewareQueue::class);
        $queue->addMiddleware(\King23\Http\Middleware\Whoops\Whoops::class);        // nicely formatted exceptions, don't use in production
        $queue->addMiddleware(\Castle23\StaticFileMiddleware::class);               // server static files, don't use in production
        $queue->addMiddleware(\King23\Http\RouterInterface::class);                 // classic king23 routing
        return $queue;
    }
);
