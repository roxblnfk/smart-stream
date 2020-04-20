<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

use roxblnfk\SmartStream\Data\DataBucket;

final class JSONConverter implements Converter
{
    public function convert(DataBucket $data): string
    {
        // of course you can use JsonSerializer
        return json_encode($data->getData(), JSON_PRETTY_PRINT|JSON_INVALID_UTF8_IGNORE|JSON_UNESCAPED_UNICODE);
    }
}
