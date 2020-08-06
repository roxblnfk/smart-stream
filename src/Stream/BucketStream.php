<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Stream;

use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Exception\ConverterNotFoundException;
use roxblnfk\SmartStream\Matching\MatchingResult;
use RuntimeException;

final class BucketStream implements StreamInterface
{
    private ConverterMatcherInterface $converterMatcher;
    private ?DataBucket $bucket;
    private ?GeneratorStream $stream = null;
    private ?MatchingResult $matchedResult = null;

    public function __construct(ConverterMatcherInterface $converterMatcher, DataBucket $bucket)
    {
        $this->bucket = $bucket;
        $this->converterMatcher = $converterMatcher;

        if ($bucket->isConvertable()) {
            $result = $this->converterMatcher->match($this->bucket);
            // if should be converted but no converter was found
            if ($result === null && $bucket->hasFormat()) {
                throw new ConverterNotFoundException($bucket->getFormat());
            }
            $this->matchedResult = $result;
        }
    }
    public function __toString(): string
    {
        try {
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }
    public function close(): void
    {
        $this->detach();
    }
    public function detach()
    {
        $this->matchedResult = null;
        $this->stream = null;
        $this->bucket = null;
        return null;
    }
    public function getSize(): ?int
    {
        return $this->stream === null ? null : $this->stream->getSize();
    }
    public function tell(): int
    {
        return $this->stream === null ? 0 : $this->stream->tell();
    }
    public function eof(): bool
    {
        if ($this->bucket === null) {
            return true;
        }
        return $this->stream === null ? false : $this->stream->eof();
    }
    public function isSeekable(): bool
    {
        return false;
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        throw new RuntimeException('Stream is not seekable.');
    }
    public function rewind(): void
    {
        if ($this->stream !== null) {
            $this->stream->rewind();
        }
    }
    public function isWritable(): bool
    {
        return false;
    }
    public function write($string): int
    {
        throw new RuntimeException('Cannot write to a non-writable stream.');
    }
    public function isReadable(): bool
    {
        return $this->initConverter() ? $this->stream->isReadable() : false;
    }
    public function read($length): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream should be rendered.');
        }
        # read generator stream
        return $this->stream->read($length);
    }
    public function getContents(): string
    {
        if ($this->bucket === null) {
            throw new RuntimeException('Unable to read stream contents.');
        }
        $content = '';
        while (!$this->eof()) {
            $content .= $this->read(PHP_INT_MAX);
        }
        return $content;
    }
    public function getMetadata($key = null)
    {
        if ($this->bucket === null) {
            return $key ? null : [];
        }

        $meta = [
            'seekable' => $this->isSeekable(),
            'eof' => $this->eof(),
        ];

        if (null === $key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function withBucket(DataBucket $bucket): self
    {
        return new static($this->converterMatcher, $bucket);
    }

    public function hasConverter(): bool
    {
        return $this->matchedResult !== null;
    }
    public function getConverter(): ?ConverterInterface
    {
        return $this->matchedResult === null ? null : $this->matchedResult->getConverter();
    }

    public function hasBucketFormat(): bool
    {
        return $this->bucket === null ? false : $this->bucket->hasFormat();
    }
    public function getBucketFormat(): ?string
    {
        return $this->bucket === null ? null : $this->bucket->getFormat();
    }

    public function hasMatchedFormat(): bool
    {
        return $this->matchedResult !== null;
    }
    public function getMatchedFormat(): ?string
    {
        return $this->matchedResult === null ? null : $this->matchedResult->getFormat();
    }

    public function isRenderStarted(): bool
    {
        return $this->stream !== null;
    }

    public function getBucket(): ?DataBucket
    {
        return $this->bucket;
    }
    public function getMatchedResult(): ?MatchingResult
    {
        return $this->matchedResult;
    }

    private function initConverter(): bool
    {
        if ($this->stream !== null) {
            return true;
        }
        if ($this->matchedResult === null || $this->bucket === null) {
            return false;
        }
        $this->stream = new GeneratorStream($this->matchedResult->getConverter()->convert($this->bucket));
        return true;
    }
}
