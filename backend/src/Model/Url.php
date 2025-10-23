<?php

namespace urli\Model;

use DateTime;

class Url
{
  public function __construct(
    public readonly int $id,
    public readonly string $originalUrl,
    public readonly string $shortCode,
    public readonly int $clicks,
    public readonly DateTime $createdAt
  ) {}

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'original_url' => $this->originalUrl,
      'short_code' => $this->shortCode,
      'clicks' => $this->clicks,
      'created_at' => $this->createdAt->format('Y-m-d H:i:s')
    ];
  }

  public function getShortUrl(string $baseUrl = 'http://localhost'): string
  {
    return rtrim($baseUrl, '/') . '/' . $this->shortCode;
  }
}
