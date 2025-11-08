<?php

namespace urli\Provider;

use urli\Container\Container;
use urli\Provider\ServiceProvider;
use urli\Repository\UserRepository;
use urli\Service\AuthService;

class AuthManagementProvider extends ServiceProvider
{
  public function register(Container $container): void
  {
    $container->set(AuthService::class, function ($c) {
      return new AuthService($c->get(UserRepository::class));
    });
  }
}
