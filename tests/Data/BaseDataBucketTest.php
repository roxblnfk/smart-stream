<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use PHPUnit\Framework\TestCase;
use roxblnfk\SmartStream\Data\DataBucket;
use Yiisoft\Http\Status;

abstract class BaseDataBucketTest extends TestCase
{
    # Immutability

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
    public function testWithoutHeadersImmutability(): void
    {
        $header = 'Custom-Header';
        $value = 'Custom Value';
        $bucket = ($this->createBucket())->withHeader($header, $value);

        $newBucket = $bucket->withoutHeaders();

        $this->assertNotSame($bucket, $newBucket);
        $this->assertTrue($bucket->hasHeader($header));
        $this->assertSame($value, $bucket->getHeaderLine($header));
        $this->assertEmpty($newBucket->getHeaders());
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

    # Format

    public function testWithNullFormat(): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withFormat('dummy-format')->withFormat(null);

        $this->assertNull($bucket->getFormat());
        $this->assertFalse($bucket->hasFormat());
    }
    public function testWithFormatParams(): void
    {
        $bucket = $this->createBucket();
        $params = ['key' => 'value'];

        $bucket = $bucket->withFormat('dummy-format', $params);

        $this->assertSame($params, $bucket->getParams());
    }
    public function testWithNullFormatParamsNotChanges(): void
    {
        $bucket = $this->createBucket();
        $params = ['key' => 'value'];

        $bucket = $bucket->withFormat('dummy-format', $params)->withFormat('new-format');

        $this->assertSame($params, $bucket->getParams());
        $this->assertSame('new-format', $bucket->getFormat());
    }

    abstract protected function createBucket(): DataBucket;
}
