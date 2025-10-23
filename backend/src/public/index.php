<?php

use Dotenv\Dotenv;
use urli\Controller\UrlController;

require __DIR__ . '/../../vendor/autoload.php';

$container = require __DIR__ . '/../config/bootstrap.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

try {
  $method = $_SERVER['REQUEST_METHOD'];
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  // Handle API routes
  if (strpos($path, '/api/') === 0) {
    handleApiRequest($container, $method, $path);
    return;
  }

  // Default response
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode([
    'error' => 'Not found',
    'message' => 'Urli API backend',
    'endpoints' => [
      'POST /api/shorten' => 'Shorten a URL',
    ]
  ]);
} catch (Exception $e) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode([
    'error' => $_ENV['APP_ENV'] === 'development'
      ? $e->getMessage()
      : 'Internal server error'
  ]);
}

function handleApiRequest($container, string $method, string $path): void
{
  header('Content-Type: application/json');

  $apiPath = preg_replace('#^/api#', '', $path);

  match ([$method, $apiPath]) {
    ['POST', '/shorten'] => $container->get(UrlController::class)->shorten(),
  };
}
