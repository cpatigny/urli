<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Repository\UrlRepository;

class RepositoryServiceProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(UrlRepository::class, function ($c) {
      return new UrlRepository($c->get('db'));
    });
  }
}
