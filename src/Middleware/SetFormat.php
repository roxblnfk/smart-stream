<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use roxblnfk\SmartStream\Stream\DataStream;

final class SetFormat implements MiddlewareInterface
{
    private string $converter;
    private ?array $params;
    /** Replace existing format */
    private bool $force;

    public function __construct(string $converter, ?array $params = [], bool $force = false)
    {
        $this->converter = $converter;
        $this->params = $params;
        $this->force = $force;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $stream = $response->getBody();
        if ($stream instanceof DataStream) {
            if ($stream->getData()->getFormat() === null || $this->force) {
                $stream->getData()->setFormat($this->converter, $this->params);
            }
        }
        return $response;
    }
}
