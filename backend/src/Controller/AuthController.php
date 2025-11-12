<?php

namespace urli\Controller;

use Exception;
use urli\Exception\ValidationException;
use urli\Http\JsonResponse;
use urli\Http\Request;
use urli\Service\AuthService;

class AuthController
{
  public function __construct(
    private AuthService $authService
  ) {}

  public function register(): void
  {
    try {
      $request = new Request();

      if (!$request->isPost()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      $data = $request->bodyData();

      // Validate required fields
      if (empty($data['email']) || empty($data['password'])) {
        JsonResponse::badRequest('Email and password are required', 'MISSING_FIELDS');
        return;
      }

      $user = $this->authService->register($data['email'], $data['password']);

      JsonResponse::created([
        'message' => 'User registered successfully',
        'user' => [
          'id' => $user->id,
          'email' => $user->email
        ]
      ]);
    } catch (ValidationException $e) {
      JsonResponse::badRequest($e->getMessage(), $e->getErrorCode());
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function login(): void
  {
    try {
      $request = new Request();

      if (!$request->isPost()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      $data = $request->bodyData();

      // Validate required fields
      if (empty($data['email']) || empty($data['password'])) {
        JsonResponse::badRequest('Email and password are required', 'MISSING_FIELDS');
        return;
      }

      $user = $this->authService->login($data['email'], $data['password']);

      JsonResponse::success([
        'message' => 'Login successful',
        'user' => [
          'id' => $user->id,
          'email' => $user->email
        ]
      ]);
    } catch (ValidationException $e) {
      JsonResponse::badRequest($e->getMessage(), $e->getErrorCode());
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function logout(): void
  {
    try {
      $request = new Request();

      if (!$request->isPost()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      $this->authService->logout();

      JsonResponse::success([
        'message' => 'Logout successful'
      ]);
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function me(): void
  {
    try {
      $user = $this->authService->getCurrentUser();

      if (!$user) {
        JsonResponse::unauthorized('Not authenticated');
        return;
      }

      JsonResponse::success([
        'user' => [
          'id' => $user->id,
          'email' => $user->email
        ]
      ]);
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function updateEmail(): void
  {
    try {
      $request = new Request();

      if (!$request->isPatch()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      $data = $request->bodyData();

      // Validate required fields
      if (empty($data['email'])) {
        JsonResponse::badRequest('Email is required', 'MISSING_FIELDS');
        return;
      }

      $user = $this->authService->updateEmail($data['email']);

      JsonResponse::success([
        'message' => 'Email updated successfully',
        'user' => [
          'id' => $user->id,
          'email' => $user->email
        ]
      ]);
    } catch (ValidationException $e) {
      JsonResponse::badRequest($e->getMessage(), $e->getErrorCode());
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function deleteMyAccount(): void
  {
    try {
      $request = new Request();

      if (!$request->isDelete()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      $user = $this->authService->getCurrentUser();

      if (!$user) {
        JsonResponse::unauthorized('Not authenticated');
        return;
      }

      $this->authService->deleteAccount($user->id);

      JsonResponse::success([
        'message' => 'Account deleted'
      ]);
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  private function handleError(Exception $e): void
  {
    error_log("Auth controller error: " . $e->getMessage());

    if (getenv('APP_ENV') === 'development') {
      JsonResponse::serverError($e->getMessage());
    } else {
      JsonResponse::serverError();
    }
  }
}
