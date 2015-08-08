<?php

// these are services relevant for the console

// the output writer
$container->register(
    \Knight23\Core\Output\WriterInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\Knight23\Core\Output\ColoredTextWriter::class);
    }
);

// the default color theme for the console
$container->register(
    \Knight23\Core\Colors\SimpleReplaceThemeInterface::class,
    function () {
        return new \Knight23\Core\Colors\SimpleReplaceTheme();
    }
);


// register a banner class for the console app
$container->register(
    \Knight23\Core\Banner\BannerInterface::class,
    function () use ($container) {
        return $container->getInstanceOf(\Castle23\Banner::class);
    }
);
