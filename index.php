<?php
use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

require 'config/constants.php';
ini_set('error_log', 'data/log/php_errors_' . date("Ymd") . '.log');
set_time_limit(0);

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
$loader = include __DIR__ . '/vendor/autoload.php';
$urlModules = include __DIR__ . '/config/url_modules.php';
foreach ($urlModules as $namespace => $path) {
    $loader->setPsr4($namespace, $path);
}
$loader->register(true);


if (! class_exists(Application::class)) {
    throw new RuntimeException("Unable to load application.\n" . "- Type `composer install` if you are developing locally.\n" . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n" . "- Type `docker-compose run zf composer install` if you are using Docker.\n");
}

// Retrieve configuration
$appConfig = require __DIR__ . '/config/application.config.php';
if (file_exists(__DIR__ . '/config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/config/development.config.php');
}


//AutoloaderFactory::factory([ClassMapAutoloader::class => [['Browser' => 'vendor/Browser.php/lib/Browser.php']]]);

// Run the application!
$application = Application::init($appConfig);

$application->run();