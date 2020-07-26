<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use roxblnfk\SmartStream\Data\WebTemplateBucket;

class WebTemplateBucketTest extends BaseDataBucketTest
{
    private const BUCKET_TEMPLATE_DATA = [];

    public function testInitialState(): void
    {
        $bucket = $this->createBucket();

        # Common methods
        $this->assertNull($bucket->getStatusCode());
        $this->assertNull($bucket->getFormat());
        $this->assertSame([], $bucket->getParams());
        $this->assertSame(self::BUCKET_TEMPLATE_DATA, $bucket->getData());
        $this->assertTrue($bucket->isConvertable());
        $this->assertFalse($bucket->hasFormat());
        # WebTemplateBucket methods
        $this->assertNull($bucket->getLayout());
        $this->assertSame([], $bucket->getLayoutData());
        $this->assertNull($bucket->getTemplate());
        $this->assertSame([], $bucket->getTemplateData());
        $this->assertSame([], $bucket->getCommonData());
    }

    # Immutability tests

    public function testWithLayoutImmutability(): void
    {
        $bucket = $this->createBucket();
        $layout = 'layout::main';

        $newBucket = $bucket->withLayout($layout);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertNull($bucket->getLayout());
        $this->assertSame($layout, $newBucket->getLayout());
    }
    public function testWithTemplateImmutability(): void
    {
        $bucket = $this->createBucket();
        $template = 'template::main/index';

        $newBucket = $bucket->withTemplate($template);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertNull($bucket->getTemplate());
        $this->assertSame($template, $newBucket->getTemplate());
    }
    public function testWithCommonDataImmutability(): void
    {
        $bucket = $this->createBucket();
        $data = ['key' => 'value'];

        $newBucket = $bucket->withCommonData($data);

        $this->assertNotSame($bucket, $newBucket);
        $this->assertSame([], $bucket->getCommonData());
        $this->assertSame($data, $newBucket->getCommonData());
    }

    protected function createBucket(): WebTemplateBucket
    {
        return new WebTemplateBucket(self::BUCKET_TEMPLATE_DATA);
    }
}
