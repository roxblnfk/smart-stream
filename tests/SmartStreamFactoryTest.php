<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests;

use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\SmartStreamFactory;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Stream\GeneratorStream;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use roxblnfk\SmartStream\Tests\Support\NullMatcher;

final class SmartStreamFactoryTest extends TestCase
{
    public function testCreateStreamFromString(): void
    {
        $factory = $this->createFactory();
        $data = 'Dummy content.';

        $stream = $factory->createStream($data);

        $this->assertNotInstanceOf(BucketStream::class, $stream);
        $this->assertNotInstanceOf(GeneratorStream::class, $stream);
    }
    public function testCreateStreamFromResource(): void
    {
        $factory = $this->createFactory();
        $resource = STDERR;

        $stream = $factory->createStream($resource);

        $this->assertNotInstanceOf(BucketStream::class, $stream);
        $this->assertNotInstanceOf(GeneratorStream::class, $stream);
    }
    public function testCreateStreamFromFileInfo(): void
    {
        $factory = $this->createFactory();
        $path = __DIR__ . '/Support/DummyFile.php';
        $file = new \SplFileInfo($path);


        $stream = $factory->createStream($file);

        $this->assertNotInstanceOf(BucketStream::class, $stream);
        $this->assertNotInstanceOf(GeneratorStream::class, $stream);
    }
    public function testCreateGeneratorStream(): void
    {
        $factory = $this->createFactory();
        $generator = (static function (iterable $from) {
            yield from $from;
        })(['f', 'o', 'o']);

        $stream = $factory->createStream($generator);

        $this->assertInstanceOf(GeneratorStream::class, $stream);
    }
    public function testCreateBucketStreamFromBucket(): void
    {
        $factory = $this->createFactory();
        $bucket = new DummyBucket();

        $stream = $factory->createStream($bucket);

        $this->assertInstanceOf(BucketStream::class, $stream);
        /** @var BucketStream $stream */
        $this->assertSame($bucket, $stream->getBucket());
    }
    public function AnyTypeValueProvider(): array
    {
        return [[array()], [null], [true], [false], [42], [42.42], [new \stdClass()]];
    }
    /**
     * @dataProvider AnyTypeValueProvider
     */
    public function testCreateBucketStreamFromAnyValue($data): void
    {
        $factory = $this->createFactory();

        $stream = $factory->createStream($data);

        $this->assertInstanceOf(BucketStream::class, $stream);
        /** @var BucketStream $stream */
        $this->assertInstanceOf(DataBucket::class, $stream->getBucket());
        $this->assertSame($data, $stream->getBucket()->getData());
    }
    public function testCreateBucketStreamWithCustomBucket(): void
    {
        $factory = $this->createFactory(DummyBucket::class);
        $data = [];

        $stream = $factory->createStream($data);

        $this->assertInstanceOf(BucketStream::class, $stream);
        /** @var BucketStream $stream */
        $this->assertInstanceOf(DummyBucket::class, $stream->getBucket());
    }

    public function testWithDefaultBucketClass(): void
    {
        $factory = $this->createFactory(DummyBucket::class);

        $newFactory = $factory->withDefaultBucketClass(DummyBucket::class);
        /** @var BucketStream $stream */
        $stream = $newFactory->createStream([]);

        // immutability
        $this->assertNotSame($factory, $newFactory);
        // bucket class
        $this->assertInstanceOf(DummyBucket::class, $stream->getBucket());
    }
    public function testWithIncorrectDefaultBucketClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createFactory(DummyBucket::class)->withDefaultBucketClass(\stdClass::class);
    }

    public function testWithStreamFactory(): void
    {
        $factory = $this->createFactory(DummyBucket::class);
        $stream = $this->createMock(StreamInterface::class);
        $calledAndSkipped = false;

        $newFactory = $factory
            ->withStreamFactory(static fn ($data) => $stream)
            ->withStreamFactory(static function ($data) use (&$calledAndSkipped) {
                $calledAndSkipped = true;
            });
        $stream1 = $factory->createStream('some value');
        $stream2 = $newFactory->createStream('some value');

        // immutability
        $this->assertNotSame($factory, $newFactory);
        $this->assertNotSame($stream1, $stream2);
        // custom factory was called by order
        $this->assertTrue($calledAndSkipped);
        $this->assertSame($stream, $stream2);
    }
    public function testWithStreamFactoryThatReturnsBucket(): void
    {
        $bucket = new DummyBucket();
        $factory = $this->createFactory()->withStreamFactory(static fn ($data) => $bucket);

        /** @var BucketStream $stream */
        $stream = $factory->createStream('some value');

        $this->assertInstanceOf(BucketStream::class, $stream);
        $this->assertSame($bucket, $stream->getBucket());
    }

    private function createFactory(string $defaultBucketClass = null): SmartStreamFactory
    {
        $params = [
            new Psr17Factory(),
            new NullMatcher(),
        ];
        $ss = new SmartStreamFactory(...$params);
        if ($defaultBucketClass !== null) {
            $ss = $ss->withDefaultBucketClass($defaultBucketClass);
        }
        return $ss;
    }
}
