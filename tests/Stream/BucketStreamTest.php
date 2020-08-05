<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream;

use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use RuntimeException;

abstract class BucketStreamTest extends BaseStreamTest
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

    # Base stream cases

    public function testGetSize(): void
    {
        $stream = $this->createStream();

        $this->assertNull($stream->getSize());
    }
    public function testIsSeekable(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->isSeekable());
    }
    public function testSeek(): void
    {
        $stream = $this->createStream();

        $this->expectException(RuntimeException::class);

        $stream->seek(5);
    }
    public function testRewindOnInit(): void
    {
        $stream = $this->createStream();

        $stream->rewind();

        $this->assertSame(0, $stream->tell());
    }
    public function testRewindAfterRead(): void
    {
        $this->markTestSkipped('Waiting issue https://bugs.php.net/bug.php?id=79927.');
        // todo: waiting issue https://bugs.php.net/bug.php?id=79927
        //
        //     $stream = $this->createStream();
        //     $x = '';
        //     $x .= $stream->read(1);
        //     $x .= $stream->read(1);
        //     $x .= $stream->read(1);
        //     $x .= $stream->read(1);
        //     $x .= $stream->read(1);
        //
        //     $generator = (function (iterable $s) { yield from $s; })([1,2,3,4]);
        //     echo $generator->current();
        //     $generator->next();
        //     echo $generator->current();
        //     $generator->rewind();
        //
        //
        //     // $this->expectException(RuntimeException::class);
        //
        //     $stream->rewind();
        //     echo $x; die;
        //     $this->assertSame(0, $stream->tell());
    }
    public function testIsWritable(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->isWritable());
    }
    public function testWrite(): void
    {
        $stream = $this->createStream();

        $this->expectException(RuntimeException::class);

        $stream->write('test');
    }

    # BucketStream methods

    abstract public function testHasConverter(): void;
    abstract public function testGetConverter(): void;
    abstract public function testHasBucketFormat(): void;
    abstract public function testGetBucketFormat(): void;
    abstract public function testHasMatchedFormat(): void;
    abstract public function testGetMatchedFormat(): void;
    abstract public function testIsRenderStarted(): void;
    abstract public function testIsRenderStartedAfterReadableCheck(): void;
    abstract public function testGetMatchedResult(): void;

    public function testGetBucket(): void
    {
        $stream = $this->createStream();

        $bucket = $stream->getBucket();

        $this->assertInstanceOf(DataBucket::class, $bucket);
    }
    public function testGetBucketAfterDetach(): void
    {
        $stream = $this->createStream();

        $stream->detach();
        $bucket = $stream->getBucket();

        $this->assertNull($bucket);
    }

    abstract protected function createStream(): BucketStream;
}
