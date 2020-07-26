<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Support;

use Generator;
use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\Data\DataBucket;

final class DummyConverter implements ConverterInterface
{
    public function convert(DataBucket $data): Generator
    {
        yield $data;
    }
}
