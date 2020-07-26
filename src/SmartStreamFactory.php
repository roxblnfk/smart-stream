<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Stream\BucketStream;
use roxblnfk\SmartStream\Stream\GeneratorStream;

final class SmartStreamFactory
{
    private StreamFactoryInterface $defaultFactory;
    private ConverterMatcherInterface $converterMatcher;
    private string $defaultBucketClass;

    public function __construct(
        StreamFactoryInterface $defaultFactory,
        ConverterMatcherInterface $converterMatcher,
        string $defaultBucketClass = DataBucket::class
    ) {
        $this->defaultFactory = $defaultFactory;
        $this->converterMatcher = $converterMatcher;
        $this->defaultBucketClass = $defaultBucketClass;
    }

    public function createStream($data, ?RequestInterface $request = null): StreamInterface
    {
        if (is_string($data)) {
            return $this->defaultFactory->createStream($data);
        }
        if ($data instanceof \SplFileInfo) {
            return $this->defaultFactory->createStreamFromFile($data->getPathname());
        }
        if (is_resource($data)) {
            return $this->defaultFactory->createStreamFromResource($data);
        }
        if ($data instanceof \Generator) {
            return new GeneratorStream($data);
        }
        if ($data instanceof DataBucket) {
            return new BucketStream($this->converterMatcher->withRequest($request), $data);
        }
        return new BucketStream($this->converterMatcher->withRequest($request), new $this->defaultBucketClass($data));
    }
}
