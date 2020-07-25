<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Matching;

use roxblnfk\SmartStream\ConverterInterface;

final class MatchingResult
{
    private string $format;
    private ConverterInterface $converter;
    private ?string $mimeType = null;

    public function __construct(string $format, ConverterInterface $converter, string $mimeType = null)
    {
        $this->format = $format;
        $this->converter = $converter;
        $this->mimeType = $mimeType;
    }
    public function getFormat(): string
    {
        return $this->format;
    }
    public function getConverter(): ConverterInterface
    {
        return $this->converter;
    }
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

}
