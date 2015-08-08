<?php
$container = new \King23\DI\DependencyContainer();

// ensure we have an APP_PATH (optional)
if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__."/..");
}

// settings service
require_once APP_PATH."/config/services/console.php";
require_once APP_PATH."/config/services/http.php";
require_once APP_PATH."/config/services/knight23.php";
require_once APP_PATH."/config/services/logger.php";
require_once APP_PATH."/config/services/react.php";
require_once APP_PATH."/config/services/settings.php";
return $container;
