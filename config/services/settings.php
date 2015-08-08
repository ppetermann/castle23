<?php

$container->register(\King23\Core\SettingsInterface::class, function() {
    return new \King23\Core\Settings();  // you should fill those in a settings file
});
