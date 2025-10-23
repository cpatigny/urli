<?php

namespace urli\Service;

use Exception;
use urli\Model\Url;
use urli\Repository\UrlRepository;

class UrlService
{
  public function __construct(private UrlRepository $urlRepository) {}

  public function shortenUrl(string $originalUrl): Url
  {
    // Validate URL
    if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
      throw new Exception('Invalid URL format');
    }

    // Generate short code
    $shortCode = $customCode ?? $this->generateShortCode();

    // Save to database
    return $this->urlRepository->save($originalUrl, $shortCode);
  }

  public function buildShortUrl(string $shortCode): string
  {
    $baseUrl = $_ENV['APP_URL'] ?: 'http://localhost';
    return rtrim($baseUrl, '/') . '/' . $shortCode;
  }

  private function generateShortCode(): string
  {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $shortCode = '';
    $length = 6;

    for ($i = 0; $i < $length; $i++) {
      $shortCode .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $shortCode;
  }

  public function formatUrlResponse(Url $url, bool $existing = false): array
  {
    return [
      'short_code' => $url->shortCode,
      'short_url' => $this->buildShortUrl($url->shortCode),
      'original_url' => $url->originalUrl,
      'created_at' => $url->createdAt->format('Y-m-d H:i:s'),
      'clicks' => $url->clicks,
      'existing' => $existing
    ];
  }

  public function getUrlByShortCode(string $shortCode): ?Url
  {
    return $this->urlRepository->findByShortCode($shortCode);
  }

  public function incrementClicks(string $shortCode): bool
  {
    return $this->urlRepository->incrementClicks($shortCode);
  }
}
