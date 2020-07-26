<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Exception;

use RuntimeException;
use Throwable;

class ConverterNotFoundException extends RuntimeException
{
    private ?string $format;
    public function __construct(?string $format, Throwable $previous = null)
    {
        $this->format = $format;
        $format = $format === null ? 'undefined' : "'{$format}'";
        parent::__construct("Converter for {$format} format not found.", 0, $previous);
    }
    public function getFormat(): ?string
    {
        return $this->format;
    }
}
