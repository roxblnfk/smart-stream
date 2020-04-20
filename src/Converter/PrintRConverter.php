<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

class PrintRConverter implements Converter
{
    public static function getFormat(): string
    {
        return 'text/plain';
    }
    public function convert($data, array $params = []): string
    {
        return print_r($data, true);
    }
}
