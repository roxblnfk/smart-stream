<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Stream\DataStream;
use roxblnfk\SmartStream\Stream\GeneratorStream;

final class SmartStreamFactory
{
    private StreamFactoryInterface $defaultFactory;
    private string $defaultDataBucket;

    public function __construct(StreamFactoryInterface $defaultFactory, string $defaultDataBucket = DataBucket::class)
    {
        $this->defaultFactory = $defaultFactory;
        $this->defaultDataBucket = $defaultDataBucket;
    }

    public function createStream($data): StreamInterface
    {
        if (is_string($data)) {
            return $this->defaultFactory->createStream($data);
        }
        if ($data instanceof \SplFileInfo) {
            return $this->defaultFactory->createStreamFromFile($data->getPath());
        }
        if (is_resource($data)) {
            return $this->defaultFactory->createStreamFromResource($data);
        }
        if ($data instanceof \Generator) {
            return new GeneratorStream($data);
        }
        if ($data instanceof DataBucket) {
            return new DataStream($data);
        }
        return new DataStream(new $this->defaultDataBucket($data));
    }
}
