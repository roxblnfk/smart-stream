<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream;

use Generator;
use PHPUnit\Framework\TestCase;
use roxblnfk\SmartStream\Stream\GeneratorStream;
use RuntimeException;

class GeneratorStreamTest extends TestCase
{
    private const DEFAULT_SEQUENCE = [0, 'foo', 1, 'bar', 42, 'baz', '', "\n", 'end'];
    private const DEFAULT_RESULT = "0foo1bar42baz\nend";

    public function testClose(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->close();

        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getSize());
    }
    public function testDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $result = $stream->detach();

        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getSize());
        $this->assertNull($result);
    }
    public function testDetachAfterDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
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
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->assertNull($stream->getSize());
    }
    public function testGetSizeAtTheEnd(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->getContents();

        $this->assertSame(strlen(self::DEFAULT_RESULT), $stream->getSize());
    }
    public function testTell(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->assertSame(0, $stream->tell());
    }
    public function testTellAfterReading(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->read(1);

        $this->assertSame(1, $stream->tell());
    }
    public function testTellAtTheEnd(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->getContents();

        $this->assertSame(strlen(self::DEFAULT_RESULT), $stream->tell());
    }
    public function testEof(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->getContents();

        $this->assertTrue($stream->eof());
    }
    public function testIsSeekable(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->assertFalse($stream->isSeekable());
    }
    public function testSeek(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->expectException(RuntimeException::class);

        $stream->seek(5);
    }
    public function testRewindOnInit(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $stream->rewind();

        $this->assertSame(0, $stream->tell());
    }
    // todo: waiting issue https://bugs.php.net/bug.php?id=79927
    // public function testRewindAfterRead(): void
    // {
    //     $stream = $this->createStream(self::DEFAULT_SEQUENCE);
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
    // }
    public function testIsWritable(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->assertFalse($stream->isWritable());
    }
    public function testWrite(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->expectException(RuntimeException::class);

        $stream->write('test');
    }
    public function testIsReadable(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $this->assertTrue($stream->isReadable());
    }
    public function testRead(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $result1 = $stream->read(4);
        $result2 = $stream->read(4);

        $this->assertSame('0', $result1);
        $this->assertSame('foo', $result2);
    }
    public function testReadAfterDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->detach();

        $this->expectException(RuntimeException::class);

        $stream->read(4);
    }
    public function testReadAtTheEnd(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->getContents();

        $this->expectException(RuntimeException::class);

        $stream->read(4);
    }
    public function testGetContents(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $result = $stream->getContents();

        $this->assertSame(self::DEFAULT_RESULT, $result);
    }
    public function testGetContentsAfterDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->detach();

        $this->expectException(RuntimeException::class);

        $stream->getContents(4);
    }
    public function testToString(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $result = (string)$stream;

        $this->assertSame(self::DEFAULT_RESULT, $result);
    }
    public function testToStringAfterDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->detach();

        $result = (string)$stream;

        $this->assertSame('', $result);
    }
    public function testGetMeta(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $result = $stream->getMetadata();

        $this->assertIsArray($result);
    }
    public function testGetMetaByKey(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);

        $result = $stream->getMetadata('eof');

        $this->assertSame($stream->eof(), $result);
    }
    public function testGetMetadataAfterDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->detach();

        $result = $stream->getMetadata();

        $this->assertSame([], $result);
    }
    public function testGetMetadataByKeyAfterDetach(): void
    {
        $stream = $this->createStream(self::DEFAULT_SEQUENCE);
        $stream->detach();

        $result = $stream->getMetadata('eof');

        $this->assertNull($result);
    }

    private function createStream(iterable $sequence): GeneratorStream
    {
        $function = static function (iterable $iterable): Generator {
            yield from $iterable;
        };
        return new GeneratorStream($function($sequence));
    }
}
