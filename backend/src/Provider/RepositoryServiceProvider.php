<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Repository\RateLimitRepository;
use urli\Repository\UrlRepository;
use urli\Repository\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(UrlRepository::class, function ($c) {
      return new UrlRepository($c->get('db'));
    });

    $container->set(UserRepository::class, function ($c) {
      return new UserRepository($c->get('db'));
    });

    $container->set(RateLimitRepository::class, function ($c) {
      return new RateLimitRepository($c->get('db'));
    });
  }
}
