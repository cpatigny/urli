<?php

namespace urli\Http;

class JsonResponse
{
  // HTTP Status Constants for better readability
  public const OK = 200;
  public const CREATED = 201;
  public const BAD_REQUEST = 400;
  public const UNAUTHORIZED = 401;
  public const NOT_FOUND = 404;
  public const METHOD_NOT_ALLOWED = 405;
  public const CONFLICT = 409;
  public const INTERNAL_SERVER_ERROR = 500;

  public static function success(array $data, int $statusCode = self::OK): void
  {
    self::sendResponse($data, $statusCode);
  }

  public static function error(string $message, string $code, int $statusCode = self::BAD_REQUEST): void
  {
    self::sendResponse([
      'error' => [
        'code' => $code,
        'message' => $message
      ]
    ], $statusCode);
  }

  public static function created(array $data): void
  {
    self::success($data, self::CREATED);
  }

  public static function notFound(string $message = 'Resource not found', string $code = 'NOT_FOUND'): void
  {
    self::error($message, $code, self::NOT_FOUND);
  }

  public static function badRequest(string $message = 'Bad request', string $code = 'BAD_REQUEST'): void
  {
    self::error($message, $code, self::BAD_REQUEST);
  }

  public static function unauthorized(string $message = 'Unauthorized', string $code = 'UNAUTHORIZED'): void
  {
    self::error($message, $code, self::UNAUTHORIZED);
  }

  public static function methodNotAllowed(string $message = 'Method not allowed', string $code = 'METHOD_NOT_ALLOWED'): void
  {
    self::error($message, $code, self::METHOD_NOT_ALLOWED);
  }

  public static function conflict(string $message = 'Resource already exists', string $code = 'CONFLICT'): void
  {
    self::error($message, $code, self::CONFLICT);
  }

  public static function serverError(string $message = 'Internal server error', string $code = 'INTERNAL_SERVER_ERROR'): void
  {
    self::error($message, $code, self::INTERNAL_SERVER_ERROR);
  }

  public static function redirect(string $url, int $statusCode = 302): void
  {
    header('Location: ' . $url, true, $statusCode);
    exit;
  }

  private static function sendResponse(array $data, int $statusCode): void
  {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
}
