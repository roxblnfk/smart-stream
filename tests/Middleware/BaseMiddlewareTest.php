<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Tests\Support\DummyBucket;
use roxblnfk\SmartStream\Tests\Support\MatcherWithAnyFormatDummyConverter;
use Yiisoft\Http\Method;

abstract class BaseMiddlewareTest extends TestCase
{
    protected function createHandler(StreamInterface $body = null): RequestHandlerInterface
    {
        return new class($body ?? $this->createStream()) implements RequestHandlerInterface {
            private StreamInterface $body;
            private ResponseInterface $response;
            public function __construct(StreamInterface $body)
            {
                $this->body = $body;
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->response = (new Response())->withBody($this->body);
                return $this->response;
            }
            public function getResponse(): ResponseInterface
            {
                return $this->response;
            }
        };
    }
    protected function createStream(ConverterMatcherInterface $matcher = null): BucketStream
    {
        return new BucketStream($matcher ?? new MatcherWithAnyFormatDummyConverter(), new DummyBucket());
    }
    protected function createServerRequest(): ServerRequestInterface
    {
        return (new Psr17Factory())
            ->createServerRequest(Method::GET, 'https://test.org/index.php');
    }
}
