<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream\BucketStream;

use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Stream\BucketStreamTest;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use roxblnfk\SmartStream\Tests\Support\NullMatcher;
use RuntimeException;

class NullBucketTest extends BucketStreamTest
{
    # Base stream cases

    public function testToString(): void
    {
        $stream = $this->createStream();

        $result = (string)$stream;

        $this->assertSame('', $result);
    }
    public function testIsReadable(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->isReadable());
    }
    public function testRead(): void
    {
        $stream = $this->createStream();

        $this->expectException(RuntimeException::class);

        $stream->read(2);
    }
    public function testGetContents(): void
    {
        $stream = $this->createStream();

        $this->expectException(RuntimeException::class);

        $stream->getContents();
    }
    public function testRewindAfterRead(): void
    {
        /** @see testRead() */
        $this->assertTrue(true);
    }

    public function testGetSizeAtTheEnd(): void
    {
        /** @see testRead() */
        $this->assertTrue(true);
    }
    public function testTellAfterReading(): void
    {
        /** @see testRead() */
        $this->assertTrue(true);
    }
    public function testTellAtTheEnd(): void
    {
        /** @see testRead() */
        $this->assertTrue(true);
    }
    public function testReadAtTheEnd(): void
    {
        /** @see testRead() */
        $this->assertTrue(true);
    }
    public function testEofAtTheEnd(): void
    {
        /** @see testRead() */
        $this->assertTrue(true);
    }

    # BucketStream methods

    public function testHasConverter(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->hasConverter());
    }
    public function testGetConverter(): void
    {
        $stream = $this->createStream();

        $converter = $stream->getConverter();

        $this->assertNull($converter);
    }
    public function testHasBucketFormat(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->hasBucketFormat());
    }
    public function testGetBucketFormat(): void
    {
        $stream = $this->createStream();

        $format = $stream->getBucketFormat();

        $this->assertNull($format);
    }
    public function testHasMatchedFormat(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->hasMatchedFormat());
    }
    public function testGetMatchedFormat(): void
    {
        $stream = $this->createStream();

        $format = $stream->getMatchedFormat();

        $this->assertNull($format);
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

        $this->assertFalse($stream->isRenderStarted());
    }
    public function testGetMatchedResult(): void
    {
        $stream = $this->createStream();

        $this->assertNull($stream->getMatchedResult());
    }

    protected function createStream(): BucketStream
    {
        return new BucketStream(new NullMatcher(), new DummyBucket());
    }
}
