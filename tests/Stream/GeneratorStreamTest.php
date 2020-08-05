<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Stream;

use Generator;
use roxblnfk\SmartStream\Stream\GeneratorStream;
use RuntimeException;

class GeneratorStreamTest extends BaseStreamTest
{
    protected const DEFAULT_SEQUENCE       = [0, 'foo', 1, 'bar', 42, 'baz', '', "\n", 'end'];
    protected const DEFAULT_CONTENT_RESULT = "0foo1bar42baz\nend";

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
    public function testRead(): void
    {
        $stream = $this->createStream();

        $result1 = $stream->read(4);
        $result2 = $stream->read(4);

        $this->assertSame('0', $result1);
        $this->assertSame('foo', $result2);
    }

    protected function createStream(): GeneratorStream
    {
        $function = static function (iterable $iterable): Generator {
            yield from $iterable;
        };
        return new GeneratorStream($function(self::DEFAULT_SEQUENCE));
    }
}
