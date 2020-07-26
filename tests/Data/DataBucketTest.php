<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use roxblnfk\SmartStream\Data\DataBucket;

class DataBucketTest extends BaseDataBucketTest
{
    private const BUCKET_DATA = [];

    public function testInitialState(): void
    {
        $bucket = $this->createBucket();

        $this->assertNull($bucket->getStatusCode());
        $this->assertNull($bucket->getFormat());
        $this->assertSame([], $bucket->getParams());
        $this->assertSame(self::BUCKET_DATA, $bucket->getData());
        $this->assertTrue($bucket->isConvertable());
        $this->assertFalse($bucket->hasFormat());
    }

    protected function createBucket(): DataBucket
    {
        return new DataBucket(self::BUCKET_DATA);
    }
}
