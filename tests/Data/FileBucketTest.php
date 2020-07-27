<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Data;

use roxblnfk\SmartStream\Data\FileBucket;
use Yiisoft\Http\Header;

final class FileBucketTest extends BaseDataBucketTest
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
        $this->assertSame([], $bucket->getHeaders());
        # FileBucket methods
        $this->assertNull($bucket->getContentType());
        $this->assertNull($bucket->getFileName());
        $this->assertFalse($bucket->hasDisposition());
        $this->assertFalse($bucket->isAttachment());
        $this->assertFalse($bucket->isInline());
    }
    public function testWithContentType(): void
    {
        $bucket = $this->createBucket();
        $type = 'application/test-format';

        $bucket = $bucket->withContentType($type);

        $this->assertSame($type, $bucket->getContentType());
        $this->assertSame($type, $bucket->getHeaderLine(Header::CONTENT_TYPE));
    }
    public function testWithNullContentType(): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withContentType('application/test-format')->withContentType(null);

        $this->assertNull($bucket->getContentType());
        $this->assertFalse($bucket->hasHeader(Header::CONTENT_TYPE));
    }

    # Immutability

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

    # Disposition type

    public function testWithAttachmentFileName(): void
    {
        $bucket = $this->createBucket();
        $fileName = 'hello-world.wav';

        $bucket = $bucket->withAttachment($fileName);

        $this->assertSame($fileName, $bucket->getFileName());
        $this->assertTrue($bucket->isAttachment());
        $this->assertTrue($bucket->hasHeader(Header::CONTENT_DISPOSITION));
    }
    public function testWithNullAttachment(): void
    {
        $bucket = $this->createBucket();
        $fileName = 'hello-world.wav';

        $bucket = $bucket->withAttachment($fileName)->withAttachment(null);

        $this->assertNull($bucket->getFileName());
        $this->assertTrue($bucket->isAttachment());
        $this->assertTrue($bucket->hasHeader(Header::CONTENT_DISPOSITION));
    }
    public function testWithAttachmentAfterInline(): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withInline()->withAttachment();

        $this->assertTrue($bucket->isAttachment());
        $this->assertFalse($bucket->isInline());
    }
    public function testWithInlineAfterAttachment(): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withAttachment()->withInline();

        $this->assertTrue($bucket->isInline());
        $this->assertFalse($bucket->isAttachment());
        $this->assertTrue($bucket->hasHeader(Header::CONTENT_DISPOSITION));
    }

    # Disposition header encoding

    public function FileNameProvider(): array
    {
        return [
            ['', 'attachment; filename=""; filename*=UTF-8\'\''],
            ['With Space', 'attachment; filename="With Space"; filename*=UTF-8\'\'With%20Space'],
            [
                "With \"quote & EOL\r\n",
                'attachment; filename="With  quote & EOL  "; filename*=UTF-8\'\'With%20%22quote%20%26%20EOL%0D%0A',
            ],
            ["\t", 'attachment; filename=" "; filename*=UTF-8\'\'%09'],
            ['dot.exe', 'attachment; filename="dot.exe"; filename*=UTF-8\'\'dot.exe'],
            [
                '!@#$%^&*()_-=+',
                'attachment; filename="!@#$%^&*()_-=+"; filename*=UTF-8\'\'%21%40%23%24%25%5E%26%2A%28%29_-%3D%2B',
            ],
            [
                'абвгдеёЯ',
                'attachment; filename="абвгдеёЯ"; filename*=UTF-8\'\'%D0%B0%D0%B1%D0%B2%D0%B3%D0%B4%D0%B5%D1%91%D0%AF',
            ],
            [
                chr(127) . chr(255),
                'attachment; filename=" ' . chr(255) . '"; filename*=UTF-8\'\'%7F%FF',
            ],
        ];
    }
    /**
     * @dataProvider FileNameProvider
     */
    public function testDispositionFileNameEncoding(?string $fileName, string $headerLine): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withAttachment($fileName);

        $this->assertSame($headerLine, $bucket->getHeaderLine(Header::CONTENT_DISPOSITION));
    }
    public function testDispositionAttachmentWithoutFilename(): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withAttachment();

        $this->assertSame('attachment', $bucket->getHeaderLine(Header::CONTENT_DISPOSITION));
    }
    public function testDispositionInline(): void
    {
        $bucket = $this->createBucket();

        $bucket = $bucket->withInline();

        $this->assertSame('inline', $bucket->getHeaderLine(Header::CONTENT_DISPOSITION));
    }

    protected function createBucket(): FileBucket
    {
        return new FileBucket(self::BUCKET_CONTENT);
    }
}
