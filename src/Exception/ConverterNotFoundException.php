<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Exception;

use RuntimeException;
use Throwable;

class ConverterNotFoundException extends RuntimeException
{
    private string $format;
    public function __construct(string $format, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->format = $format;
    }
}
