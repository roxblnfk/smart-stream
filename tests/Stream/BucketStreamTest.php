<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream;

use PHPUnit\Framework\TestCase;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use roxblnfk\SmartStream\Tests\Support\NullMatcher;

class BucketStreamTest extends TestCase
{
    # Immutability

    public function testWithBucketImmutability(): void
    {
        $stream = $this->createStream();
        $bucket = new DummyBucket();

        $newStream = $stream->withBucket($bucket);

        $this->assertNotSame($stream, $newStream);
        $this->assertNotSame($bucket, $stream->getBucket());
        $this->assertSame($bucket, $newStream->getBucket());
    }


    private function createStream(): BucketStream
    {
        return new BucketStream(new NullMatcher(), new DummyBucket());
    }
}
