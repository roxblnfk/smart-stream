<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use roxblnfk\SmartStream\Data\FileBucket;

class FileBucketTest extends BaseDataBucketTest
{
    private const BUCKET_CONTENT = 'Default bucket content';

    public function testInitialState(): void
    {
        $bucket = $this->createBucket();

        # Common methods
        $this->assertNull($bucket->getStatusCode());
        $this->assertNull($bucket->getFormat());
        $this->assertSame([], $bucket->getParams());
        $this->assertSame(self::BUCKET_CONTENT, $bucket->getData());
        $this->assertFalse($bucket->isConvertable());
        $this->assertFalse($bucket->hasFormat());
        # FileBucket methods
        $this->assertNull($bucket->getContentType());
        $this->assertNull($bucket->getFileName());
        $this->assertFalse($bucket->hasDisposition());
        $this->assertFalse($bucket->isAttachment());
        $this->assertFalse($bucket->isInline());
    }

    # Immutability tests

    public function testWithContentTypeImmutability(): void
    {
        $bucket = $this->createBucket();
        $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        $newBucket = $bucket->withContentType($type);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertNull($bucket->getContentType());
        $this->assertSame($type, $newBucket->getContentType());
    }
    public function testWithAttachmentImmutability(): void
    {
        $bucket = $this->createBucket();

        $newBucket = $bucket->withAttachment();

        $this->assertNotSame($bucket, $newBucket);
        $this->assertFalse($bucket->hasDisposition());
        $this->assertFalse($bucket->isAttachment());
        $this->assertTrue($newBucket->hasDisposition());
        $this->assertTrue($newBucket->isAttachment());
    }
    public function testWithInlineImmutability(): void
    {
        $bucket = $this->createBucket();

        $newBucket = $bucket->withInline();

        $this->assertNotSame($bucket, $newBucket);
        $this->assertFalse($bucket->hasDisposition());
        $this->assertFalse($bucket->isInline());
        $this->assertTrue($newBucket->hasDisposition());
        $this->assertTrue($newBucket->isInline());
    }

    protected function createBucket(): FileBucket
    {
        return new FileBucket(self::BUCKET_CONTENT);
    }
}
