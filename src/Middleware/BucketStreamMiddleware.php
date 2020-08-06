<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use roxblnfk\SmartStream\Stream\BucketStream;
use Yiisoft\Http\Header;

final class BucketStreamMiddleware implements MiddlewareInterface
{
    private StreamFactoryInterface $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if (!$stream instanceof BucketStream) {
            return $response;
        }

        $bucket = $stream->getBucket();

        // Bucket has been detached
        if ($bucket === null) {
            return $stream->isReadable() ? $response : $response->withBody($this->streamFactory->createStream(''));
        }

        if (!$stream->hasConverter() && !$bucket->isConvertable() && !$stream->isReadable()) {
            $response = $response->withBody($this->createReadableStream($bucket->getData()));
        }

        // Set MIME type
        $matching = $stream->getMatchedResult();
        if ($matching !== null && $matching->getMimeType() !== null) {
            $response = $response->withHeader(Header::CONTENT_TYPE, $matching->getMimeType());
        }

        // Update request
        if ($bucket->getStatusCode() !== null) {
            $response = $response->withStatus($bucket->getStatusCode());
        }
        return $this->addHeaderList($response, $bucket->getHeaders());
    }

    private function addHeaderList(ResponseInterface $response, array $headers): ResponseInterface
    {
        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }
        return $response;
    }
    private function createReadableStream($data): StreamInterface
    {
        if ($data instanceof \SplFileInfo) {
            return $this->streamFactory->createStreamFromFile($data->getRealPath());
        }
        if (is_resource($data)) {
            return $this->streamFactory->createStreamFromResource($data);
        }
        return $this->streamFactory->createStream(is_string($data) ? $data : '');
    }
}
