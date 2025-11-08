<?php

namespace urli\Model;

use DateTime;

class User
{
  public function __construct(
    public readonly int $id,
    public readonly string $email,
    public readonly string $passwordHash,
    public readonly DateTime $createdAt,
    public readonly DateTime $updatedAt
  ) {}

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'email' => $this->email,
      'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
      'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
    ];
  }

  public function verifyPassword(string $password): bool
  {
    return password_verify($password, $this->passwordHash);
  }
}
