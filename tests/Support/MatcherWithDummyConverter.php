<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Support;

use Psr\Http\Message\RequestInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Matching\MatchingResult;

final class MatcherWithDummyConverter implements ConverterMatcherInterface
{
    public const FORMAT_NAME = 'dummy-format';
    public function withRequest(?RequestInterface $request): ConverterMatcherInterface
    {
        return $this;
    }
    public function match(DataBucket $bucket): ?MatchingResult
    {
        return $bucket->getFormat() === self::FORMAT_NAME
            ? new MatchingResult(self::FORMAT_NAME, new DummyConverter())
            : null;
    }
}
