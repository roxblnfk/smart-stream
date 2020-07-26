<?php

namespace roxblnfk\SmartStream\Data;

use SplFileInfo;
use Yiisoft\Http\Header;

class FileBucket extends DataBucket
{
    public const TYPE_OCTET_STREAM = 'application/octet-stream';

    protected const DISPOSITION_INLINE = 'inline';
    protected const DISPOSITION_ATTACHMENT = 'attachment';

    protected const IS_CONVERTABLE = false;
    protected ?string $contentType = null;
    protected ?string $contentDisposition = null;
    protected ?string $fileName = null;

    /**
     * FileBucket constructor.
     * @param string|resource|SplFileInfo $data
     * @param null|string $contentType
     * @param null|string $filename
     * @throws \Exception
     */
    public function __construct($data, string $contentType = null, string $filename = null)
    {
        switch (true) {
            case $data instanceof SplFileInfo:
                $filename = $filename ?? $data->getFilename();
                $contentType = $contentType ?? $this->contentType($filename);
                parent::__construct($data);
                break;
            case is_string($data):
            case is_resource($data):
                parent::__construct($data);
                break;
            default:
                throw new \Exception('The $data parameter must be a resource, a string or an instance of SplFileInfo');
        }

        if ($contentType !== null) {
            $this->setContentType($contentType);
        }
        if ($filename !== null) {
            $this->setAttachment($filename);
        }
    }

    public static function createFromPath(string $filePath): self
    {
        return new static(new SplFileInfo($filePath));
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
    public function getFileName(): ?string
    {
        return $this->fileName;
    }
    public function hasDisposition(): bool
    {
        return $this->contentDisposition !== null;
    }
    public function isAttachment(): bool
    {
        return $this->contentDisposition === self::DISPOSITION_ATTACHMENT;
    }
    public function isInline(): bool
    {
        return $this->contentDisposition === self::DISPOSITION_INLINE;
    }

    public function withAttachment(string $filename = null): self
    {
        $clone = clone $this;
        $clone->setAttachment($filename);
        return $clone;
    }
    public function withContentType(?string $contentType): self
    {
        $clone = clone $this;
        $clone->setContentType($contentType);
        return $clone;
    }
    public function withInline(): self
    {
        $clone = clone $this;
        $clone->setInline();
        return $clone;
    }

    public function setContentType(?string $contentType): void
    {
        $this->contentType = $contentType;
        $this->setHeader(Header::CONTENT_TYPE, $contentType);
    }
    public function setInline(): void
    {
        $this->contentDisposition = self::DISPOSITION_INLINE;
        $this->setDispositionHeader();
    }
    final protected function setAttachment(?string $filename): void
    {
        $this->contentDisposition = self::DISPOSITION_ATTACHMENT;
        $this->fileName = $filename;
        $this->setDispositionHeader();
    }
    final protected function setDispositionHeader(): void
    {
        if ($this->contentDisposition === null) {
            return;
        }
        $headerBody = ($this->contentDisposition === self::DISPOSITION_ATTACHMENT && $this->fileName !== null)
            ? sprintf(
                '%s; filename="%s"; filename*=UTF-8\'\'%s',
                $this->contentDisposition,
                preg_replace('/[\x00-\x1F\x7F\"]/', ' ', $this->fileName),
                rawurlencode($this->fileName)
            )
            : $this->contentDisposition;
        $this->setHeader(Header::CONTENT_DISPOSITION, $headerBody);
    }
    private function contentType(?string $filename): ?string
    {
        if ($filename === null) {
            return null;
        }
        $result = function_exists('mime_content_type') ? mime_content_type($filename) : null;
        return is_string($result) ? $result : null;
    }
}
