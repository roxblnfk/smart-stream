<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Data\FileBucket;
use roxblnfk\SmartStream\Middleware\BucketStreamMiddleware;
use roxblnfk\SmartStream\Middleware\SetFormat;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use roxblnfk\SmartStream\Tests\Support\MatcherWithAnyFormatDummyConverter;
use roxblnfk\SmartStream\Tests\Support\MatcherWithDummyConverter;
use roxblnfk\SmartStream\Tests\Support\NullMatcher;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;

final class BucketStreamMiddlewareTest extends BaseMiddlewareTest
{
    private const HEADER_NAME = 'Test-Header';
    private const HEADER_VALUE = 'Test value';

    public function testNotBucketStream()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        $handler = $this->createHandler(Stream::create(''));

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertNotInstanceOf(BucketStream::class, $stream);
        $this->assertSame($handler->getResponse(), $response);
    }
    public function testWithExistingConverter()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        $stream = $this->createBucketStreamWithConverter(
            (new DummyBucket())->withHeader(self::HEADER_NAME, self::HEADER_VALUE)->withStatusCode(Status::CONTINUE)
        );
        $handler = $this->createHandler($stream);

        $response = $middleware->process($request, $handler);

        $this->assertNotSame($handler->getResponse(), $response);
        $this->assertTrue($response->hasHeader(self::HEADER_NAME));
        $this->assertTrue($response->hasHeader(Header::CONTENT_TYPE));
        $this->assertSame(self::HEADER_VALUE, $response->getHeaderLine(self::HEADER_NAME));
        $this->assertSame(MatcherWithDummyConverter::MIME, $response->getHeaderLine(Header::CONTENT_TYPE));
        $this->assertSame(Status::CONTINUE, $response->getStatusCode());
        $this->assertSame($stream, $response->getBody());
    }
    public function testWithDetachedStream()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        $stream = $this->createBucketStreamWithConverter(
            (new DummyBucket())->withHeader(self::HEADER_NAME, self::HEADER_VALUE)
        );
        $handler = $this->createHandler($stream);
        $stream->detach();

        $response = $middleware->process($request, $handler);

        $this->assertNotSame($handler->getResponse(), $response);
        $this->assertNotSame($stream, $response->getBody());
        $this->assertFalse($response->hasHeader(self::HEADER_NAME));
    }
    public function testConverterMimeTypeRewrittenFromBucket()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        $stream = $this->createBucketStreamWithConverter(
            (new DummyBucket())->withHeader(Header::CONTENT_TYPE, 'text/other-plain-text')
        );
        $handler = $this->createHandler($stream);

        $response = $middleware->process($request, $handler);

        $this->assertTrue($response->hasHeader(Header::CONTENT_TYPE));
        $this->assertSame('text/other-plain-text', $response->getHeaderLine(Header::CONTENT_TYPE));
    }
    public function testNotConvertableBucketStream()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        $stream = $this->createBucketStreamWithoutConverter(
            (new FileBucket('File Content'))->withHeader(self::HEADER_NAME, self::HEADER_VALUE)
        );
        $handler = $this->createHandler($stream);

        $response = $middleware->process($request, $handler);
        $response->getBody()->rewind();

        $this->assertNotSame($handler->getResponse(), $response);
        $this->assertNotSame($stream, $response->getBody());
        $this->assertTrue($response->hasHeader(self::HEADER_NAME));
        $this->assertSame(self::HEADER_VALUE, $response->getHeaderLine(self::HEADER_NAME));
        $this->assertSame('File Content', $response->getBody()->getContents());
    }
    public function testNotConvertableBucketStreamFromFileInfo()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        $stream = $this->createBucketStreamWithoutConverter(
            (FileBucket::createFromPath(__DIR__ . '/../Support/DummyFile.php'))
        );
        $handler = $this->createHandler($stream);

        $response = $middleware->process($request, $handler);
        $response->getBody()->rewind();

        $this->assertNotSame($handler->getResponse(), $response);
        $this->assertNotSame($stream, $response->getBody());
    }
    public function testNotConvertableBucketStreamFromResource()
    {
        $middleware = $this->createMiddleware();
        $request = $this->createServerRequest();
        try {
            $fp = fopen(__DIR__ . '/../Support/DummyFile.php', 'r');
            $stream = $this->createBucketStreamWithoutConverter((new FileBucket($fp)));
            $handler = $this->createHandler($stream);

            $response = $middleware->process($request, $handler);
            $response->getBody()->rewind();

            $this->assertNotSame($handler->getResponse(), $response);
            $this->assertNotSame($stream, $response->getBody());
        } finally {
            fclose($fp);
        }
    }

    private function createBucketStreamWithConverter(DataBucket $bucket): BucketStream
    {
        return new BucketStream(
            new MatcherWithDummyConverter(),
            $bucket->withFormat(MatcherWithDummyConverter::FORMAT_NAME)
        );
    }
    private function createBucketStreamWithoutConverter(DataBucket $bucket): BucketStream
    {
        return new BucketStream(new NullMatcher(), $bucket);
    }
    private function createMiddleware(): BucketStreamMiddleware
    {
        return new BucketStreamMiddleware(new Psr17Factory());
    }
}
