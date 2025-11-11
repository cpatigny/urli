<?php

namespace urli\Service;

use Exception;
use urli\Exception\ValidationException;
use urli\Model\User;
use urli\Repository\UserRepository;

class AuthService
{
  public function __construct(
    private UserRepository $userRepository
  ) {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function register(string $email, string $password): User
  {
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new ValidationException('Invalid email format', 'INVALID_EMAIL');
    }

    // Validate password length
    if (strlen($password) < 6) {
      throw new ValidationException('Password must be at least 6 characters', 'INVALID_PASSWORD');
    }

    // Check if email already exists
    if ($this->userRepository->findByEmail($email)) {
      throw new ValidationException('Email already exists', 'EMAIL_EXISTS');
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Create user
    $user = $this->userRepository->create($email, $passwordHash);

    // Log user in automatically after registration
    $this->setUserSession($user);

    return $user;
  }

  public function login(string $email, string $password): User
  {
    // Find user by email
    $user = $this->userRepository->findByEmail($email);

    if (!$user) {
      throw new ValidationException('Invalid email or password', 'INVALID_CREDENTIALS');
    }

    // Verify password
    if (!$user->verifyPassword($password)) {
      throw new ValidationException('Invalid email or password', 'INVALID_CREDENTIALS');
    }

    // Set session
    $this->setUserSession($user);

    return $user;
  }

  public function logout(): void
  {
    // Clear session
    $_SESSION = [];

    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy session
    session_destroy();
  }

  public function deleteAccount(int $id): void
  {
    // Logout first to clean up session before deleting the user
    $this->logout();

    $deleted = $this->userRepository->delete($id);

    if (!$deleted) {
      throw new Exception('Failed to delete account');
    }
  }

  public function getCurrentUser(): ?User
  {
    if (!isset($_SESSION['user_id'])) {
      return null;
    }

    return $this->userRepository->findById($_SESSION['user_id']);
  }

  public function isAuthenticated(): bool
  {
    return isset($_SESSION['user_id']);
  }

  private function setUserSession(User $user): void
  {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_email'] = $user->email;
  }
}
