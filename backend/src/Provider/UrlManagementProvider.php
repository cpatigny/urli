<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Provider\ServiceProvider;
use urli\Repository\UrlRepository;
use urli\Service\UrlService;

class UrlManagementProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(UrlService::class, function ($c) {
      return new UrlService($c->get(UrlRepository::class));
    });
  }
}
