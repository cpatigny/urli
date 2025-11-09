<?php

namespace urli\Repository;

use DateTime;
use Exception;
use PDO;
use urli\Exception\ValidationException;
use urli\Model\Url;

class UrlRepository extends BaseRepository
{
  public function __construct(protected PDO $db) {}

  public function save(string $originalUrl, string $shortCode, ?int $userId = null): Url
  {
    try {
      $stmt = $this->db->prepare(
        "INSERT INTO urls (original_url, short_code, user_id, created_at) VALUES (?, ?, ?, NOW())"
      );

      $success = $stmt->execute([$originalUrl, $shortCode, $userId]);

      if (!$success) {
        throw new Exception('Failed to save URL');
      }

      $id = $this->db->lastInsertId();
      return $this->findById($id);
    } catch (\PDOException $e) {
      if ($e->getCode() == 23000) { // Duplicate entry
        throw new ValidationException('Short code already exists', 'SHORT_CODE_EXISTS');
      }
      throw new Exception('Database error: ' . $e->getMessage());
    }
  }

  public function findByShortCode(string $shortCode): ?Url
  {
    $stmt = $this->db->prepare("SELECT * FROM urls WHERE short_code = ?");
    $stmt->execute([$shortCode]);

    $data = $stmt->fetch();
    return $data ? $this->hydrate($data) : null;
  }

  public function findByOriginalUrl(string $originalUrl): ?Url
  {
    $stmt = $this->db->prepare("SELECT * FROM urls WHERE original_url = ?");
    $stmt->execute([$originalUrl]);

    $data = $stmt->fetch();
    return $data ? $this->hydrate($data) : null;
  }

  public function findById(int $id): ?Url
  {
    $stmt = $this->db->prepare("SELECT * FROM urls WHERE id = ?");
    $stmt->execute([$id]);

    $data = $stmt->fetch();
    return $data ? $this->hydrate($data) : null;
  }

  public function incrementClicks(string $shortCode): bool
  {
    $stmt = $this->db->prepare(
      "UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?"
    );

    return $stmt->execute([$shortCode]);
  }

  private function hydrate(array $data): Url
  {
    return new Url(
      id: (int) $data['id'],
      originalUrl: $data['original_url'],
      shortCode: $data['short_code'],
      userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
      clicks: (int) $data['clicks'],
      createdAt: new DateTime($data['created_at'])
    );
  }
}
