<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Support;

use Psr\Http\Message\RequestInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Matching\MatchingResult;

final class MatcherWithAnyFormatDummyConverter implements ConverterMatcherInterface
{
    public function withRequest(?RequestInterface $request): ConverterMatcherInterface
    {
        return $this;
    }
    public function match(DataBucket $bucket): ?MatchingResult
    {
        return $bucket->getFormat() !== null
            ? new MatchingResult($bucket->getFormat(), new DummyConverter())
            : null;
    }
}
