<?php

namespace urli\Service;

use PDO;
use urli\Repository\RateLimitRepository;

class RateLimitService
{
  private const MAX_ANONYMOUS_REQUESTS_PER_DAY = 10;
  private const MAX_USER_REQUESTS_PER_DAY = 30;
  private const WINDOW_MINUTES = 1440; // 24 hours
  private const CLEANUP_INTERVAL = 86400; // 24 hours in seconds

  public function __construct(
    private RateLimitRepository $rateLimitRepository,
    private PDO $db
  ) {}

  public function checkRateLimitForAnonymous(string $ipAddress): bool
  {
    $count = $this->rateLimitRepository->countRequestsByIpInWindow(
      $ipAddress,
      self::WINDOW_MINUTES
    );

    return $count < self::MAX_ANONYMOUS_REQUESTS_PER_DAY;
  }

  public function checkRateLimitForUser(int $userId): bool
  {
    $count = $this->rateLimitRepository->countRequestsByUserInWindow(
      $userId,
      self::WINDOW_MINUTES
    );

    return $count < self::MAX_USER_REQUESTS_PER_DAY;
  }

  public function recordRequest(?string $ipAddress = null, ?int $userId = null): void
  {
    $this->rateLimitRepository->recordRequest($ipAddress, $userId);
  }

  public function cleanupOldRecords(): int
  {
    // Clean up records older than 24 hours
    return $this->rateLimitRepository->cleanupOldRecords(self::WINDOW_MINUTES);
  }

  public function shouldCleanup(): bool
  {
    $stmt = $this->db->prepare(
      "SELECT config_value FROM system_config WHERE config_key = 'rate_limit_last_cleanup'"
    );
    $stmt->execute();
    $result = $stmt->fetch();

    // If no record exists, we should cleanup
    if (!$result) {
      return true;
    }

    $lastCleanup = (int) $result['config_value'];
    $now = time();

    // Cleanup every 24 hours
    return ($now - $lastCleanup) > self::CLEANUP_INTERVAL;
  }

  public function markCleanupDone(): void
  {
    $now = time();

    $stmt = $this->db->prepare(
      "INSERT INTO system_config (config_key, config_value, updated_at)
       VALUES ('rate_limit_last_cleanup', ?, NOW())
       ON DUPLICATE KEY UPDATE config_value = ?, updated_at = NOW()"
    );
    $stmt->execute([$now, $now]);
  }

  public function getClientIpAddress(): string
  {
    // Check for IP from various headers (for proxy/load balancer support)
    $headers = [
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_REAL_IP',
      'HTTP_CLIENT_IP',
      'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
      if (!empty($_SERVER[$header])) {
        $ip = $_SERVER[$header];
        // If X-Forwarded-For contains multiple IPs, get the first one
        if (strpos($ip, ',') !== false) {
          $ip = trim(explode(',', $ip)[0]);
        }
        // Validate IP address
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
          return $ip;
        }
      }
    }

    return '0.0.0.0'; // Fallback
  }
}
