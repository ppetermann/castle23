<?php
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
        /** @var \King23\Http\MiddlewareQueue $queue */
        $queue = $container->getInstanceOf(\King23\Http\MiddlewareQueue::class);
        $queue->addMiddleware(\King23\Http\Middleware\Whoops\Whoops::class);        // nicely formatted exceptions, don't use in production
        $queue->addMiddleware(\Castle23\StaticFileMiddleware::class);               // server static files, don't use in production
        $queue->addMiddleware(\King23\Http\RouterInterface::class);                 // classic king23 routing
        return $queue;
    }
);
