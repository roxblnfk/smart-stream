<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use PHPUnit\Framework\TestCase;
use roxblnfk\SmartStream\Data\DataBucket;
use Yiisoft\Http\Status;

abstract class BaseDataBucketTest extends TestCase
{
    # Immutability tests

    public function testWithStatusCodeImmutability(): void
    {
        $bucket = $this->createBucket();

        $newBucket = $bucket->withStatusCode(Status::SEE_OTHER);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertNotSame(Status::SEE_OTHER, $bucket->getStatusCode());
        $this->assertSame(Status::SEE_OTHER, $newBucket->getStatusCode());
    }
    public function testWithHeaderImmutability(): void
    {
        $bucket = $this->createBucket();
        $header = 'Custom-Header';
        $value = 'Custom Value';

        $newBucket = $bucket->withHeader($header, $value);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertFalse($bucket->hasHeader($header));
        $this->assertNull($bucket->getHeaderLine($header));
        $this->assertTrue($newBucket->hasHeader($header));
        $this->assertSame($value, $newBucket->getHeaderLine($header));
    }
    public function testWithoutHeaderImmutability(): void
    {
        $header = 'Custom-Header';
        $value = 'Custom Value';
        $bucket = ($this->createBucket())->withHeader($header, $value);

        $newBucket = $bucket->withoutHeader($header);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertTrue($bucket->hasHeader($header));
        $this->assertSame($value, $bucket->getHeaderLine($header));
        $this->assertFalse($newBucket->hasHeader($header));
        $this->assertNull($newBucket->getHeaderLine($header));
    }
    public function testWithFormatImmutability(): void
    {
        $bucket = $this->createBucket();
        $format = 'custom-format';

        $newBucket = $bucket->withFormat($format);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertNull($bucket->getFormat());
        $this->assertSame($format, $newBucket->getFormat());
        if ($bucket->isConvertable()) {
            $this->assertFalse($bucket->hasFormat());
            $this->assertTrue($newBucket->hasFormat());
        }
    }

    abstract protected function createBucket(): DataBucket;
}
