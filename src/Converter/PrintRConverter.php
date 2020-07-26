<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

use Generator;
use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\Data\DataBucket;

class PrintRConverter implements ConverterInterface
{
    public function convert(DataBucket $data): Generator
    {
        yield print_r($data, true);
    }
}
