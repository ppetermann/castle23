<?php
// logger
$container->register(
    \Psr\Log\LoggerInterface::class,
    function () {
        return new \Devedge\Log\NoLog();        // you probably want to use something like monolog here
    }
);
