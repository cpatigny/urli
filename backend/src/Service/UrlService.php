<?php

namespace urli\Service;

use Exception;
use urli\Exception\ValidationException;
use urli\Model\Url;
use urli\Model\UrlResult;
use urli\Repository\UrlRepository;

class UrlService
{
  public function __construct(private UrlRepository $urlRepository) {}

  public function shortenUrl(string $originalUrl, ?string $customCode = null, ?int $userId = null): UrlResult
  {
    // If logged-in user without custom code, check if they already shortened this URL
    if ($userId !== null && $customCode === null) {
      $existingUrl = $this->urlRepository->findByOriginalUrlAndUserId($originalUrl, $userId);
      if ($existingUrl) {
        return new UrlResult($existingUrl, true);
      }
    }

    // Generate short code
    $shortCode = $customCode ?? $this->generateShortCode();

    // Ensure uniqueness if auto-generated
    if ($customCode === null) {
      $attempts = 0;
      while ($this->urlRepository->findByShortCode($shortCode) && $attempts < 10) {
        $shortCode = $this->generateShortCode();
        $attempts++;
      }

      if ($attempts >= 10) {
        throw new Exception('Failed to generate unique short code');
      }
    }

    // Save to database
    $newUrl = $this->urlRepository->save($originalUrl, $shortCode, $userId);

    return new UrlResult($newUrl, false);
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

  public function formatUrlResultResponse(UrlResult $result): array
  {
    return $this->formatUrlResponse($result->getUrl(), $result->isExisting());
  }

  public function isExistingUrl(string $shortCode, string $originalUrl): bool
  {
    $existingUrl = $this->getUrlByShortCode($shortCode);
    return $existingUrl && $existingUrl->originalUrl === $originalUrl;
  }

  public function getUrlByShortCode(string $shortCode): ?Url
  {
    return $this->urlRepository->findByShortCode($shortCode);
  }

  public function incrementClicks(string $shortCode): bool
  {
    return $this->urlRepository->incrementClicks($shortCode);
  }

  public function deleteUrl(string $shortCode, int $userId): bool
  {
    // Find URL first
    $url = $this->urlRepository->findByShortCode($shortCode);

    if (!$url) {
      throw new ValidationException('URL not found', 'URL_NOT_FOUND');
    }

    // Check ownership
    if ($url->userId !== $userId) {
      throw new ValidationException('You do not own this URL', 'FORBIDDEN');
    }

    // Delete URL
    return $this->urlRepository->deleteByShortCodeAndUserId($shortCode, $userId);
  }

  public function getUserUrls(int $userId): array
  {
    $urls = $this->urlRepository->findByUserId($userId);

    return array_map(function (Url $url) {
      return [
        'short_code' => $url->shortCode,
        'short_url' => $this->buildShortUrl($url->shortCode),
        'original_url' => $url->originalUrl,
        'created_at' => $url->createdAt->format('Y-m-d H:i:s'),
        'clicks' => $url->clicks
      ];
    }, $urls);
  }
}
