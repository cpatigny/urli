<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Controller\UrlController;
use urli\Provider\ServiceProvider;
use urli\Repository\UrlRepository;
use urli\Service\UrlService;

class ControllerServiceProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(UrlController::class, function ($c) {
      return new UrlController(
        $c->get(UrlRepository::class),
        $c->get(UrlService::class)
      );
    });
  }
}
