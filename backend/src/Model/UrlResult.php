<?php

namespace urli\Model;

class UrlResult
{
  public function __construct(
    public readonly Url $url,
    public readonly bool $isExisting
  ) {}

  public function getUrl(): Url
  {
    return $this->url;
  }

  public function isExisting(): bool
  {
    return $this->isExisting;
  }

  public function isNew(): bool
  {
    return !$this->isExisting;
  }
}
