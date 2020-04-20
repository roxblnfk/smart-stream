<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

use roxblnfk\SmartStream\Data\DataBucket;

class PrintRConverter implements Converter
{
    public static function getFormat(): string
    {
        return 'text/plain';
    }
    public function convert(DataBucket $data): string
    {
        return print_r($data, true);
    }
}
