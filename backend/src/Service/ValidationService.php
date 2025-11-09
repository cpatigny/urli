<?php

namespace urli\Service;

class ValidationService
{

  // Returns a message if there is an error or returns null otherwise
  public function validateShortenRequest(array $data): ?string
  {
    // Check required fields
    if (!isset($data['url']) || empty(trim($data['url']))) {
      return 'URL is required';
    }

    $url = trim($data['url']);

    if (strlen($url) > 2000) {
      return 'URL is too long';
    }

    // Validate URL format
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      return 'Invalid URL format';
    }

    // Validate custom code format if provided
    if (!empty($data['custom_code'])) {
      $customCode = trim($data['custom_code']);

      if (strlen($customCode) < 3 || strlen($customCode) > 20) {
        return 'Custom code must be between 3-20 characters';
      }

      if (!preg_match('/^[a-zA-Z0-9_-]+$/', $customCode)) {
        return 'Custom code can only contain letters, numbers, hyphens, and underscores';
      }
    }

    return null; // No validation errors
  }

  public function validateShortCode(string $shortCode): ?string
  {
    if (empty($shortCode)) {
      return 'Short code is required';
    }

    if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $shortCode)) {
      return 'Invalid short code format';
    }

    return null; // Valid
  }
}
