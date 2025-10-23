<?php

namespace urli\Repository;

use DateTime;
use Exception;
use PDO;
use urli\Model\Url;

class UrlRepository extends BaseRepository
{
  public function __construct(protected PDO $db) {}

  public function save(string $originalUrl, string $shortCode): Url
  {
    try {
      $stmt = $this->db->prepare(
        "INSERT INTO urls (original_url, short_code, created_at) VALUES (?, ?, NOW())"
      );

      $success = $stmt->execute([$originalUrl, $shortCode]);

      if (!$success) {
        throw new Exception('Failed to save URL');
      }

      $id = $this->db->lastInsertId();
      return $this->findById($id);
    } catch (\PDOException $e) {
      if ($e->getCode() == 23000) { // Duplicate entry
        throw new Exception('Short code already exists');
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
      clicks: (int) $data['clicks'],
      createdAt: new DateTime($data['created_at'])
    );
  }
}
