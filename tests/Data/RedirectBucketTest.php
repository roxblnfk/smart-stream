<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use roxblnfk\SmartStream\Data\RedirectBucket;
use Yiisoft\Http\Status;

class RedirectBucketTest extends BaseDataBucketTest
{
    private const BUCKET_URL = '/';

    public function testInitialState(): void
    {
        $bucket = $this->createBucket();

        # Common methods
        $this->assertSame(Status::FOUND, $bucket->getStatusCode());
        $this->assertNull($bucket->getFormat());
        $this->assertSame([], $bucket->getParams());
        $this->assertSame('', $bucket->getData());
        $this->assertFalse($bucket->isConvertable());
        $this->assertFalse($bucket->hasFormat());
        # RedirectBucket methods
        $this->assertSame(self::BUCKET_URL, $bucket->getLocation());
    }

    # Immutability tests

    public function testWithLocationImmutability(): void
    {
        $bucket = $this->createBucket();
        $url = 'https://my-project.pro/not-found';

        $newBucket = $bucket->withLocation($url);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertSame(self::BUCKET_URL, $bucket->getLocation());
        $this->assertSame($url, $newBucket->getLocation());
    }

    protected function createBucket(): RedirectBucket
    {
        return new RedirectBucket(self::BUCKET_URL);
    }
}
