<?php

use urli\Container\Container;
use urli\Provider\ControllerServiceProvider;
use urli\Provider\DatabaseServiceProvider;
use urli\Provider\RepositoryServiceProvider;
use urli\Provider\UrlManagementProvider;

require_once __DIR__ . '/../../vendor/autoload.php';

$container = new Container();

$providers = [
  new DatabaseServiceProvider(),
  new RepositoryServiceProvider(),
  new ControllerServiceProvider(),
  new UrlManagementProvider(),
];

foreach ($providers as $provider) {
  $provider->register($container);
}

return $container;
