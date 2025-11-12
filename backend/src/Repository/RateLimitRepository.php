<?php

namespace urli\Repository;

use DateTime;
use PDO;
use urli\Model\RateLimit;

class RateLimitRepository extends BaseRepository
{
  public function __construct(protected PDO $db) {}

  public function recordRequest(?string $ipAddress = null, ?int $userId = null): void
  {
    $stmt = $this->db->prepare(
      "INSERT INTO rate_limits (ip_address, user_id, created_at) VALUES (?, ?, NOW())"
    );
    $stmt->execute([$ipAddress, $userId]);
  }

  public function countRequestsByIpInWindow(string $ipAddress, int $windowMinutes): int
  {
    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as count FROM rate_limits
       WHERE ip_address = ?
       AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
    );
    $stmt->execute([$ipAddress, $windowMinutes]);
    $result = $stmt->fetch();
    return (int) $result['count'];
  }

  public function countRequestsByUserInWindow(int $userId, int $windowMinutes): int
  {
    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as count FROM rate_limits
       WHERE user_id = ?
       AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
    );
    $stmt->execute([$userId, $windowMinutes]);
    $result = $stmt->fetch();
    return (int) $result['count'];
  }

  public function cleanupOldRecords(int $olderThanMinutes): int
  {
    $stmt = $this->db->prepare(
      "DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL ? MINUTE)"
    );
    $stmt->execute([$olderThanMinutes]);
    return $stmt->rowCount();
  }
}
