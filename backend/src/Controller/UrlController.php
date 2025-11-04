<?php

namespace urli\Controller;

use Exception;
use urli\Http\JsonResponse;
use urli\Http\Request;
use urli\Service\UrlService;
use urli\Service\ValidationService;

class UrlController
{
  public function __construct(
    private UrlService $urlService,
    private ValidationService $validationService
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
        JsonResponse::badRequest($validationError);
        return;
      }

      $originalUrl = $request->getUrl();
      $customCode = $request->getCustomCode();

      $result = $this->urlService->shortenUrl($originalUrl, $customCode);

      $responseData = $this->urlService->formatUrlResultResponse($result);
      JsonResponse::created($responseData);
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
