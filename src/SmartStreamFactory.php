<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Stream\GeneratorStream;

final class SmartStreamFactory
{
    private ConverterMatcherInterface $converterMatcher;
    private string $defaultBucketClass = DataBucket::class;
    private array $factories = [];

    public function __construct(
        StreamFactoryInterface $defaultFactory,
        ConverterMatcherInterface $converterMatcher
    ) {
        $this->converterMatcher = $converterMatcher;

        $this->addStreamFactory(static fn($data) => $data instanceof \Generator ? new GeneratorStream($data) : null);
        $this->addStreamFactory(function ($data) use ($defaultFactory) {
            switch (true) {
                case is_string($data):
                    return $defaultFactory->createStream($data);
                case is_resource($data):
                    return $defaultFactory->createStreamFromResource($data);
                case $data instanceof \SplFileInfo:
                    return $defaultFactory->createStreamFromFile($data->getPathname());
            }
            return null;
        });
    }

    public function withDefaultBucketClass(string $bucketClass): self
    {
        $clone = clone $this;
        if (!is_subclass_of($bucketClass, DataBucket::class, true)) {
            throw new \InvalidArgumentException('Bucket class should be subclass of DataBucket.');
        }
        $clone->defaultBucketClass = $bucketClass;
        return $clone;
    }

    /**
     * Last added factory called first
     */
    public function withStreamFactory(Closure $factory): self
    {
        $clone = clone $this;
        $clone->addStreamFactory($factory);
        return $clone;
    }

    public function withoutStreamFactories(): self
    {
        $clone = clone $this;
        $clone->factories = [];
        return $clone;
    }

    private function addStreamFactory(Closure $factory): void
    {
        array_unshift($this->factories, $factory);
    }

    public function createStream($data, ?RequestInterface $request = null): StreamInterface
    {
        foreach ($this->factories as $factory) {
            $result = $factory($data);
            if ($result instanceof StreamInterface) {
                return $result;
            }
            if ($result instanceof DataBucket) {
                $data = $result;
                break;
            }
        }
        return new BucketStream(
            $this->converterMatcher->withRequest($request),
            $data instanceof DataBucket ? $data : new $this->defaultBucketClass($data)
        );
    }
}
