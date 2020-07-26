<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Stream;

use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Exception\ConverterNotFoundException;
use roxblnfk\SmartStream\Matching\MatchingResult;

final class BucketStream implements StreamInterface
{
    private ConverterMatcherInterface $converterMatcher;
    private ?DataBucket $bucket;
    private ?GeneratorStream $stream = null;
    private bool $calculated = false;
    private ?MatchingResult $matchedResult = null;

    public function __construct(ConverterMatcherInterface $converterMatcher, DataBucket $bucket)
    {
        $this->bucket = $bucket;
        $this->converterMatcher = $converterMatcher;
        $this->prepareConverter();
    }
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
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
        $result = $this->bucket;
        $this->bucket = null;
        return $result;
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
        return $this->stream === null ? false : $this->stream->eof();
    }
    public function isSeekable(): bool
    {
        return false;
    }
    public function seek($offset, $whence = \SEEK_SET): void
    {
        throw new \RuntimeException('Stream is not seekable.');
    }
    public function rewind(): void
    {
        if ($this->stream !== null) {
            $this->stream->rewind();
            $this->caret = 0;
            $this->started = false;
        }
    }
    public function isWritable(): bool
    {
        return false;
    }
    public function write($string): int
    {
        throw new \RuntimeException('Cannot write to a non-writable stream.');
    }
    public function isReadable(): bool
    {
        if ($this->matchedResult === null) {
            return false;
        }
        $this->initConverter();
        return $this->stream === null ? false : $this->stream->isReadable();
    }
    public function read($length): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream should be rendered.');
        }
        # read generator stream
        return $this->stream->read($length);
    }
    public function getContents(): string
    {
        return $this->read(PHP_INT_MAX);
    }
    public function getMetadata($key = null)
    {
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
    public function hasFormat(): bool
    {
        return $this->matchedResult !== null;
    }
    public function getFormat(): ?string
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
    private function prepareConverter(): void
    {
        if ($this->calculated) {
            return;
        }
        // if should be converted
        if ($this->bucket->isFormatable()) {
            $result = $this->converterMatcher->match($this->bucket);
            if ($result === null) {
                throw new ConverterNotFoundException((string)$this->bucket->getFormat());
            }
            $this->matchedResult = $result;
        }
        $this->calculated = true;
    }
}
