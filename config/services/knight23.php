<?php
// register the main application itself
$container->register(
    \Knight23\Core\RunnerInterface::class,
    function () use ($container) {
        // instance for the class
        return $container->getInstanceOf(\Knight23\Core\Knight23::class);
    }
);
