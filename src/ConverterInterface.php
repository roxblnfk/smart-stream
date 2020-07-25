<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream;

use Generator;
use roxblnfk\SmartStream\Data\DataBucket;

interface ConverterInterface
{
    public function convert(DataBucket $data): Generator;
}
