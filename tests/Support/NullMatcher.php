<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Support;

use Psr\Http\Message\RequestInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Matching\MatchingResult;

final class NullMatcher implements ConverterMatcherInterface
{
    public function withRequest(?RequestInterface $request): ConverterMatcherInterface
    {
        return $this;
    }
    public function match(DataBucket $bucket): ?MatchingResult
    {
        return null;
    }
}
