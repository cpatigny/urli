<?php

namespace urli\Provider;

use urli\Container\Container;

abstract class ServiceProvider
{
  abstract public function register(Container $container): void;
}
