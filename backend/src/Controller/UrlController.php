<?php

namespace urli\Controller;

use Exception;
use urli\Repository\UrlRepository;
use urli\Service\UrlService;

class UrlController
{
  public function __construct(private UrlRepository $urlRepository, private UrlService $urlService) {}

  public function shorten(): void
  {
    try {
      // Set JSON response headers
      header('Content-Type: application/json');

      // Validate request method
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
      }

      // Get and decode JSON input
      $input = file_get_contents('php://input');
      $data = json_decode($input, true);

      // Check for JSON decode errors
      if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
      }

      // Validate required fields
      if (!isset($data['url']) || empty(trim($data['url']))) {
        http_response_code(400);
        echo json_encode(['error' => 'URL is required']);
        return;
      }

      $originalUrl = trim($data['url']);

      // Validate URL format
      if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid URL format']);
        return;
      }

      // Check if URL was already shortened
      $existingUrl = $this->urlRepository->findByOriginalUrl($originalUrl);
      if ($existingUrl) {
        http_response_code(200);
        echo json_encode([
          'success' => true,
          'data' => $this->urlService->formatUrlResponse($existingUrl, true)
        ]);
        return;
      }

      // Create shortened URL
      $shortenedUrl = $this->urlService->shortenUrl($originalUrl);

      // Success response
      http_response_code(201);
      echo json_encode([
        'success' => true,
        'data' => $this->urlService->formatUrlResponse($shortenedUrl, false)
      ]);
    } catch (Exception $e) {
      error_log("URL shortening error: " . $e->getMessage());

      http_response_code(500);
      echo json_encode([
        'error' => $_ENV['APP_ENV'] === 'development'
          ? $e->getMessage()
          : 'Internal server error'
      ]);
    }
  }
}
