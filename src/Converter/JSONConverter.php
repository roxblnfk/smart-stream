<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

use Generator;
use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\Data\DataBucket;

final class JSONConverter implements ConverterInterface
{
    public function convert(DataBucket $data): Generator
    {
        // of course you can use JsonSerializer
        yield json_encode($data->getData(), JSON_PRETTY_PRINT|JSON_INVALID_UTF8_IGNORE|JSON_UNESCAPED_UNICODE);
    }
}
