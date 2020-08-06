<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream\BucketStream;

use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Exception\ConverterNotFoundException;
use roxblnfk\SmartStream\Matching\MatchingResult;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Stream\BucketStreamTest;
use roxblnfk\SmartStream\Tests\Support\DummyConverter;
use roxblnfk\SmartStream\Tests\Support\MatcherWithDummyConverter;
use RuntimeException;

class DummyBucketTest extends BucketStreamTest
{
    # Base stream cases

    public function testTellAfterReading(): void
    {
        $stream = $this->createStream();
        $stream->read(1);

        $this->assertSame(strlen(static::DEFAULT_CONTENT_RESULT), $stream->tell());
    }
    public function testRead(): void
    {
        $stream = $this->createStream();

        $result1 = $stream->read(2);

        // DummyConverter
        $this->assertSame(static::DEFAULT_CONTENT_RESULT, $result1);
    }

    public function testRewindAfterReadableCheck(): void
    {
        $stream = $this->createStream();
        $stream->isReadable();

        $stream->rewind();

        # no error
        $this->assertSame(0, $stream->tell());
    }

    # BucketStream methods

    public function testHasConverter(): void
    {
        $stream = $this->createStream();

        $this->assertTrue($stream->hasConverter());
    }
    public function testGetConverter(): void
    {
        $stream = $this->createStream();

        $converter = $stream->getConverter();

        $this->assertInstanceOf(DummyConverter::class, $converter);
    }
    public function testHasBucketFormat(): void
    {
        $stream = $this->createStream();

        $this->assertTrue($stream->hasBucketFormat());
    }
    public function testGetBucketFormat(): void
    {
        $stream = $this->createStream();

        $format = $stream->getBucketFormat();

        $this->assertSame(MatcherWithDummyConverter::FORMAT_NAME, $format);
    }
    public function testHasMatchedFormat(): void
    {
        $stream = $this->createStream();

        $this->assertTrue($stream->hasMatchedFormat());
    }
    public function testGetMatchedFormat(): void
    {
        $stream = $this->createStream();

        $format = $stream->getMatchedFormat();

        $this->assertSame(MatcherWithDummyConverter::FORMAT_NAME, $format);
    }
    public function testIsRenderStarted(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->isRenderStarted());
    }
    public function testIsRenderStartedAfterReadableCheck(): void
    {
        $stream = $this->createStream();

        $stream->isReadable();

        $this->assertTrue($stream->isRenderStarted());
    }
    public function testGetMatchedResult(): void
    {
        $stream = $this->createStream();

        $result = $stream->getMatchedResult();

        $this->assertInstanceOf(MatchingResult::class, $result);
        $this->assertInstanceOf(DummyConverter::class, $result->getConverter());
        $this->assertSame(MatcherWithDummyConverter::FORMAT_NAME, $result->getFormat());
        $this->assertSame(MatcherWithDummyConverter::MIME, $result->getMimeType());
    }

    # No converter cases

    public function testCreateWithUnsupportedFormat(): void
    {
        $this->expectException(ConverterNotFoundException::class);

        new BucketStream(
            new MatcherWithDummyConverter(),
            (new DataBucket(static::DEFAULT_CONTENT_RESULT))->withFormat('unsupported-format')
        );
    }

    protected function createStream(): BucketStream
    {
        return new BucketStream(
            new MatcherWithDummyConverter(),
            (new DataBucket(static::DEFAULT_CONTENT_RESULT))->withFormat(MatcherWithDummyConverter::FORMAT_NAME)
        );
    }
}
