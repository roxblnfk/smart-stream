<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

interface Converter
{
    public function convert($data, array $params = []): string;
}
