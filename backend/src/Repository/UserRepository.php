<?php

namespace urli\Repository;

use DateTime;
use Exception;
use PDO;
use urli\Model\User;

class UserRepository extends BaseRepository
{
  public function __construct(protected PDO $db) {}

  public function create(string $email, string $passwordHash): User
  {
    try {
      $stmt = $this->db->prepare(
        "INSERT INTO users (email, password_hash, created_at, updated_at)
         VALUES (?, ?, NOW(), NOW())"
      );

      $success = $stmt->execute([$email, $passwordHash]);

      if (!$success) {
        throw new Exception('Failed to create user');
      }

      $id = $this->db->lastInsertId();
      return $this->findById($id);
    } catch (\PDOException $e) {
      if ($e->getCode() == 23000) {
        throw new Exception('Email already exists');
      }
      throw new Exception('Database error: ' . $e->getMessage());
    }
  }

  public function findById(int $id): ?User
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);

    $data = $stmt->fetch();
    return $data ? $this->hydrate($data) : null;
  }

  public function findByEmail(string $email): ?User
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $data = $stmt->fetch();
    return $data ? $this->hydrate($data) : null;
  }

  public function delete(int $id): bool
  {
    $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
    $success = $stmt->execute([$id]);

    return $success && $stmt->rowCount() > 0;
  }

  private function hydrate(array $data): User
  {
    return new User(
      id: (int) $data['id'],
      email: $data['email'],
      passwordHash: $data['password_hash'],
      createdAt: new DateTime($data['created_at']),
      updatedAt: new DateTime($data['updated_at'])
    );
  }
}
