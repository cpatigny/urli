<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Provider\ServiceProvider;
use urli\Repository\UrlRepository;
use urli\Service\UrlService;
use urli\Service\ValidationService;

class UrlManagementProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(ValidationService::class, function ($c) {
      return new ValidationService($c->get(UrlRepository::class));
    });

    $container->set(UrlService::class, function ($c) {
      return new UrlService($c->get(UrlRepository::class));
    });
  }
}
