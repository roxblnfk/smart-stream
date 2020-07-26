<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
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
    public function testCreateBucketStreamFromArray(): void
    {
        $factory = $this->createFactory();
        $data = [];

        $stream = $factory->createStream($data);

        $this->assertInstanceOf(BucketStream::class, $stream);
        /** @var BucketStream $stream */
        $this->assertInstanceOf(DataBucket::class, $stream->getBucket());
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

    private function createFactory(string $defaultBucketClass = null): SmartStreamFactory
    {
        $params = [
            new Psr17Factory(),
            new NullMatcher(),
        ];
        if ($defaultBucketClass !== null) {
            $params[] = $defaultBucketClass;
        }
        return new SmartStreamFactory(...$params);
    }
}
