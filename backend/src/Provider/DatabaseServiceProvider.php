<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Database\DatabaseFactory;

class DatabaseServiceProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set('db_config', function () {
      return require __DIR__ . '/../config/database.php';
    });

    $container->set(DatabaseFactory::class, function ($c) {
      return new DatabaseFactory($c->get('db_config'));
    });

    $container->set('db', function ($c) {
      return $c->get(DatabaseFactory::class)->create();
    });
  }
}
