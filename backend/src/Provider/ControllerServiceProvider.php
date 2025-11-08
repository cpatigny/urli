<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Controller\AuthController;
use urli\Controller\UrlController;
use urli\Provider\ServiceProvider;
use urli\Repository\UrlRepository;
use urli\Service\AuthService;
use urli\Service\UrlService;
use urli\Service\ValidationService;

class ControllerServiceProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(UrlController::class, function ($c) {
      return new UrlController(
        $c->get(UrlService::class),
        $c->get(ValidationService::class)
      );
    });

    $container->set(AuthController::class, function ($c) {
      return new AuthController(
        $c->get(AuthService::class)
      );
    });
  }
}
