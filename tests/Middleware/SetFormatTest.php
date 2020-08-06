<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Middleware;

use Nyholm\Psr7\Stream;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Data\FileBucket;
use roxblnfk\SmartStream\Middleware\SetFormat;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Support\MatcherWithAnyFormatDummyConverter;
use roxblnfk\SmartStream\Tests\Support\MatcherWithDummyConverter;

final class SetFormatTest extends BaseMiddlewareTest
{
    public function testSetFormatWeak(): void
    {
        $format = MatcherWithDummyConverter::FORMAT_NAME;
        $middleware = (new SetFormat($format));
        $request = $this->createServerRequest();
        $handler = $this->createHandler($this->createStream(new MatcherWithDummyConverter()));

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertFalse($stream->isRenderStarted());
        $this->assertSame($format, $stream->getBucketFormat());
    }
    public function testSetFormatWeakWithExistingFormat(): void
    {
        $format = MatcherWithDummyConverter::FORMAT_NAME;
        $middleware = (new SetFormat($format));
        $request = $this->createServerRequest();
        $handler = $this->createHandler(
            new BucketStream(new MatcherWithAnyFormatDummyConverter(), new DataBucket('', 'first-format'))
        );

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertFalse($stream->isRenderStarted());
        $this->assertSame('first-format', $stream->getBucketFormat());
    }
    public function testForceSetFormatWeakWithExistingFormat(): void
    {
        $format = MatcherWithDummyConverter::FORMAT_NAME;
        $middleware = (new SetFormat($format, null, true));
        $request = $this->createServerRequest();
        $handler = $this->createHandler(
            new BucketStream(new MatcherWithAnyFormatDummyConverter(), new DataBucket('', 'first-format'))
        );

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertFalse($stream->isRenderStarted());
        $this->assertSame($format, $stream->getBucketFormat());
    }
    public function testSetFormatWithParams(): void
    {
        $format = MatcherWithDummyConverter::FORMAT_NAME;
        $params = ['depth' => 42];
        $middleware = (new SetFormat($format, $params));
        $request = $this->createServerRequest();
        $handler = $this->createHandler($this->createStream(new MatcherWithAnyFormatDummyConverter()));

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertFalse($stream->isRenderStarted());
        $this->assertSame($format, $stream->getBucketFormat());
        $this->assertSame($params, $stream->getBucket()->getParams());
    }

    public function testNotBucketStream()
    {
        $format = MatcherWithDummyConverter::FORMAT_NAME;
        $middleware = (new SetFormat($format));
        $request = $this->createServerRequest();
        $handler = $this->createHandler(Stream::create(''));

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertNotInstanceOf(BucketStream::class, $stream);
    }
    public function testNotConvertableBucketStream()
    {
        $format = MatcherWithDummyConverter::FORMAT_NAME;
        $middleware = (new SetFormat($format));
        $request = $this->createServerRequest();
        $handler = $this->createHandler(
            new BucketStream(new MatcherWithDummyConverter(), new FileBucket(''))
        );

        $response = $middleware->process($request, $handler);
        /** @var BucketStream $stream */
        $stream = $response->getBody();

        $this->assertFalse($stream->isRenderStarted());
        $this->assertNotSame($format, $stream->getBucketFormat());
    }
}
