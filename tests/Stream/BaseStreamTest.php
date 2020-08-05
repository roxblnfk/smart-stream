<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

abstract class BaseStreamTest extends TestCase
{
    protected const DEFAULT_CONTENT_RESULT = 'test string data';

    public function testClose(): void
    {
        $stream = $this->createStream();
        $stream->close();

        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getSize());
    }
    public function testDetach(): void
    {
        $stream = $this->createStream();
        $result = $stream->detach();

        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getSize());
        $this->assertNull($result);
    }
    public function testDetachAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();
        # no error
        $result = $stream->detach();

        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getSize());
        $this->assertNull($result);
    }
    public function testGetSize(): void
    {
        $stream = $this->createStream();

        $this->assertSame(strlen(static::DEFAULT_CONTENT_RESULT), $stream->getSize());
    }
    public function testGetSizeAtTheEnd(): void
    {
        $stream = $this->createStream();
        $stream->getContents();

        $this->assertSame(strlen(static::DEFAULT_CONTENT_RESULT), $stream->getSize());
    }
    public function testTell(): void
    {
        $stream = $this->createStream();

        $this->assertSame(0, $stream->tell());
    }
    public function testTellAfterReading(): void
    {
        $stream = $this->createStream();
        $stream->read(1);

        $this->assertSame(1, $stream->tell());
    }
    public function testTellAtTheEnd(): void
    {
        $stream = $this->createStream();
        $stream->getContents();

        $this->assertSame(strlen(static::DEFAULT_CONTENT_RESULT), $stream->tell());
    }
    public function testEof(): void
    {
        $stream = $this->createStream();

        $this->assertFalse($stream->eof());
    }
    public function testEofAtTheEnd(): void
    {
        $stream = $this->createStream();
        $stream->getContents();

        $this->assertTrue($stream->eof());
    }
    abstract public function testIsSeekable(): void;
    abstract public function testSeek(): void;
    abstract public function testRewindOnInit();
    abstract public function testRewindAfterRead();
    abstract public function testIsWritable();
    abstract public function testWrite(): void;
    public function testIsReadable(): void
    {
        $stream = $this->createStream();

        $this->assertTrue($stream->isReadable());
    }
    public function testRead(): void
    {
        $stream = $this->createStream();

        $result1 = $stream->read(2);
        $result2 = $stream->read(2);

        $this->assertSame('te', $result1);
        $this->assertSame('st', $result2);
    }
    public function testReadAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();

        $this->expectException(RuntimeException::class);

        $stream->read(4);
    }
    public function testReadAtTheEnd(): void
    {
        $stream = $this->createStream();
        $stream->getContents();

        $this->expectException(RuntimeException::class);

        $stream->read(4);
    }
    public function testGetContents(): void
    {
        $stream = $this->createStream();

        $result = $stream->getContents();

        $this->assertSame(static::DEFAULT_CONTENT_RESULT, $result);
    }
    public function testGetContentsAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();

        $this->expectException(RuntimeException::class);

        $stream->getContents();
    }
    public function testToString(): void
    {
        $stream = $this->createStream();

        $result = (string)$stream;

        $this->assertSame(static::DEFAULT_CONTENT_RESULT, $result);
    }
    public function testToStringAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();

        $result = (string)$stream;

        $this->assertSame('', $result);
    }
    public function testGetMeta(): void
    {
        $stream = $this->createStream();

        $result = $stream->getMetadata();

        $this->assertIsArray($result);
    }
    public function testGetMetaByKey(): void
    {
        $stream = $this->createStream();

        $result = $stream->getMetadata('eof');

        $this->assertSame($stream->eof(), $result);
    }
    public function testGetMetadataAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();

        $result = $stream->getMetadata();

        $this->assertSame([], $result);
    }
    public function testGetMetadataByKeyAfterDetach(): void
    {
        $stream = $this->createStream();
        $stream->detach();

        $result = $stream->getMetadata('eof');

        $this->assertNull($result);
    }

    abstract protected function createStream(): StreamInterface;
}
