<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream;

use Psr\Http\Message\RequestInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Matching\MatchingResult;

interface ConverterMatcherInterface
{
    public function withRequest(?RequestInterface $request): self;
    public function match(DataBucket $bucket): ?MatchingResult;
}
