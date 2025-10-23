<?php

namespace urli\Container;

use Exception;

class Container
{
  private array $services = [];
  private array $instances = []; // cache instances

  public function set(string $name, callable $factory): void
  {
    $this->services[$name] = $factory;
  }

  public function get(string $name)
  {
    // check cache first
    if (isset($this->instances[$name])) {
      return $this->instances[$name];
    }

    // check if service exists
    if (!isset($this->services[$name])) {
      throw new Exception("Service {$name} not found");
    }

    // create instance
    $instance = $this->services[$name]($this);

    // cache the instance
    $this->instances[$name] = $instance;

    return $instance;
  }

  public function clearCache(): void
  {
    $this->instances = [];
  }
}
