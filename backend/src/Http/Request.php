<?php

namespace urli\Http;

class Request
{
  private array $bodyData;
  private array $queryData;
  private string $method;
  private string $path;

  public function __construct()
  {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $this->bodyData = $this->parseBodyData();
    $this->queryData = $_GET;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getPath(): string
  {
    return $this->path;
  }

  public function get(string $key, $default = null)
  {
    // Check body data first (POST/PUT), then query params (GET)
    if (isset($this->bodyData[$key])) {
      return $this->bodyData[$key];
    }

    if (isset($this->queryData[$key])) {
      return $this->queryData[$key];
    }

    return $default;
  }

  public function getFromBody(string $key, $default = null)
  {
    return $this->bodyData[$key] ?? $default;
  }

  public function getFromQuery(string $key, $default = null)
  {
    return $this->queryData[$key] ?? $default;
  }

  public function has(string $key): bool
  {
    return isset($this->bodyData[$key]) || isset($this->queryData[$key]);
  }

  public function hasInBody(string $key): bool
  {
    return isset($this->bodyData[$key]);
  }

  public function hasInQuery(string $key): bool
  {
    return isset($this->queryData[$key]);
  }

  public function all(): array
  {
    return array_merge($this->queryData, $this->bodyData);
  }


  public function bodyData(): array
  {
    return $this->bodyData;
  }

  public function queryData(): array
  {
    return $this->queryData;
  }

  public function isPost(): bool
  {
    return $this->method === 'POST';
  }

  public function isGet(): bool
  {
    return $this->method === 'GET';
  }

  public function isPut(): bool
  {
    return $this->method === 'PUT';
  }

  public function isDelete(): bool
  {
    return $this->method === 'DELETE';
  }

  public function isPatch(): bool
  {
    return $this->method === 'PATCH';
  }

  public function getCustomCode(): ?string
  {
    $customCode = $this->getFromBody('custom_code');

    if (empty($customCode)) {
      return null;
    }

    return trim($customCode);
  }

  public function getUrl(): string
  {
    return trim($this->getFromBody('url', ''));
  }

  private function parseBodyData(): array
  {
    if (!in_array($this->method, ['POST', 'PUT', 'PATCH'])) {
      return [];
    }

    $input = file_get_contents('php://input');

    if (empty($input)) {
      return [];
    }

    $decoded = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return [];
    }

    return $decoded ?? [];
  }
}
