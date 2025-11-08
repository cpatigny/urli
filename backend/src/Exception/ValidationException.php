<?php

namespace urli\Exception;

use Exception;

class ValidationException extends Exception
{
  private string $errorCode;

  public function __construct(string $message, string $errorCode)
  {
    parent::__construct($message);
    $this->errorCode = $errorCode;
  }

  public function getErrorCode(): string
  {
    return $this->errorCode;
  }
}
