<?php

namespace urli\Controller;

use Exception;
use urli\Exception\ValidationException;
use urli\Http\JsonResponse;
use urli\Http\Request;
use urli\Service\AuthService;
use urli\Service\UrlService;
use urli\Service\ValidationService;

class UrlController
{
  public function __construct(
    private UrlService $urlService,
    private ValidationService $validationService,
    private AuthService $authService
  ) {}

  public function shorten(): void
  {
    try {
      $request = new Request();

      if (!$request->isPost()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      $requestData = $request->bodyData();
      $validationError = $this->validationService->validateShortenRequest($requestData);

      if ($validationError) {
        JsonResponse::badRequest($validationError, 'VALIDATION_ERROR');
        return;
      }

      $originalUrl = $request->getUrl();
      $customCode = $request->getCustomCode();

      if ($customCode !== null && !$this->authService->isAuthenticated()) {
        JsonResponse::unauthorized('You must be logged in to use custom short codes', 'AUTHENTICATION_REQUIRED');
        return;
      }

      $userId = null;
      if ($this->authService->isAuthenticated()) {
        $currentUser = $this->authService->getCurrentUser();
        $userId = $currentUser ? $currentUser->id : null;
      }

      $result = $this->urlService->shortenUrl($originalUrl, $customCode, $userId);

      $responseData = $this->urlService->formatUrlResultResponse($result);
      JsonResponse::created($responseData);
    } catch (ValidationException $e) {
      JsonResponse::badRequest($e->getMessage(), $e->getErrorCode());
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function redirect(string $shortCode): void
  {
    try {
      $validationError = $this->validationService->validateShortCode($shortCode);
      if ($validationError) {
        JsonResponse::notFound('Invalid short code');
      }

      $url = $this->urlService->getUrlByShortCode($shortCode);
      if (!$url) {
        JsonResponse::notFound('URL not found');
        return;
      }

      $this->urlService->incrementClicks($shortCode);
      JsonResponse::redirect($url->originalUrl);
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function getUserUrls(): void
  {
    try {
      $request = new Request();

      if (!$request->isGet()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      if (!$this->authService->isAuthenticated()) {
        JsonResponse::unauthorized('Authentication required', 'UNAUTHORIZED');
        return;
      }

      $currentUser = $this->authService->getCurrentUser();
      $urls = $this->urlService->getUserUrls($currentUser->id);

      JsonResponse::success(['urls' => $urls]);
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  public function deleteUrl(string $shortCode): void
  {
    try {
      $request = new Request();

      if (!$request->isDelete()) {
        JsonResponse::methodNotAllowed();
        return;
      }

      if (!$this->authService->isAuthenticated()) {
        JsonResponse::unauthorized('Authentication required', 'UNAUTHORIZED');
        return;
      }

      $currentUser = $this->authService->getCurrentUser();
      $deleted = $this->urlService->deleteUrl($shortCode, $currentUser->id);

      if (!$deleted) {
        JsonResponse::notFound('URL not found', 'URL_NOT_FOUND');
        return;
      }

      JsonResponse::success(['message' => 'URL deleted successfully']);
    } catch (ValidationException $e) {
      if ($e->getErrorCode() === 'FORBIDDEN') {
        JsonResponse::notFound('URL not found', 'URL_NOT_FOUND');
      } else {
        JsonResponse::badRequest($e->getMessage(), $e->getErrorCode());
      }
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  private function handleError(Exception $e): void
  {
    error_log("URL controller error: " . $e->getMessage());

    if (getenv('APP_ENV') === 'development') {
      JsonResponse::serverError($e->getMessage());
    } else {
      JsonResponse::serverError();
    }
  }
}
