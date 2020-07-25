<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use roxblnfk\SmartStream\Stream\BucketStream;

final class SetFormat implements MiddlewareInterface
{
    private string $format;
    private ?array $params;
    /** Replace existing format */
    private bool $force;

    public function __construct(string $format, ?array $params = [], bool $force = false)
    {
        $this->format = $format;
        $this->params = $params;
        $this->force = $force;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if ($stream instanceof BucketStream) {
            $data = $stream->getBucket();
            if ($data !== null && $data->isFormatable() and !$stream->hasFormat() || $this->force) {
                return $response->withBody($stream->withBucket($data->withFormat($this->format, $this->params)));
            }
        }
        return $response;
    }
}
