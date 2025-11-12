<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Provider\ServiceProvider;
use urli\Repository\RateLimitRepository;
use urli\Repository\UrlRepository;
use urli\Service\RateLimitService;
use urli\Service\UrlService;
use urli\Service\ValidationService;

class UrlManagementProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(ValidationService::class, function ($c) {
      return new ValidationService();
    });

    $container->set(UrlService::class, function ($c) {
      return new UrlService($c->get(UrlRepository::class));
    });

    $container->set(RateLimitService::class, function ($c) {
      return new RateLimitService(
        $c->get(RateLimitRepository::class),
        $c->get('db')
      );
    });
  }
}
