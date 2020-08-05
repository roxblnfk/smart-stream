<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream;

use PHPUnit\Framework\TestCase;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use roxblnfk\SmartStream\Tests\Support\MatcherWithDummyConverter;
use roxblnfk\SmartStream\Tests\Support\NullMatcher;
use RuntimeException;

class BucketStreamTest extends BaseStreamTest
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
    public function testGetContentsAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();

        $this->expectException(RuntimeException::class);

        $stream->getContents();
    }

    protected function createNullStream(): BucketStream
    {
        return new BucketStream(new NullMatcher(), new DummyBucket());
    }
    protected function createStream(): BucketStream
    {
        return new BucketStream(
            new MatcherWithDummyConverter(),
            (new DataBucket(static::DEFAULT_CONTENT_RESULT))->withFormat(MatcherWithDummyConverter::FORMAT_NAME)
        );
    }
}
