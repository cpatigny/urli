<?php

namespace urli\Database;

use Exception;
use PDO;
use PDOException;

class DatabaseFactory
{
  private array $config;

  public function __construct(array $config)
  {
    $this->config = $config;
  }
  public function create(): PDO
  {
    try {
      $dsn = $this->buildDsn();

      $pdo = new PDO(
        $dsn,
        $this->config['username'],
        $this->config['password'],
        $this->config['options'],
      );

      return $pdo;
    } catch (PDOException $e) {
      if ($_ENV['APP_ENV'] === 'development') {
        throw new Exception('Database connection failed: ' . $e->getMessage());
      }

      throw new Exception('Database connection failed');
    }
  }

  private function buildDsn(): string
  {
    return sprintf(
      "mysql:host=%s;dbname=%s;charset=%s",
      $this->config['host'],
      $this->config['dbname'],
      $this->config['charset'] ?? 'utf8mb4'
    );
  }
}
