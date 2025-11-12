<?php

namespace urli\Model;

use DateTime;

class RateLimit
{
  public function __construct(
    public readonly int $id,
    public readonly string $ipAddress,
    public readonly DateTime $createdAt
  ) {}
}
