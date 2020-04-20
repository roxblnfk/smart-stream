<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Converter;

use roxblnfk\SmartStream\Data\DataBucket;

interface Converter
{
    public function convert(DataBucket $data): string;
}
