<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Support;

use roxblnfk\SmartStream\Data\DataBucket;

final class DummyBucket extends DataBucket
{
    public function __construct()
    {
        parent::__construct(null);
    }
}
