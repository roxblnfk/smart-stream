<?php

namespace roxblnfk\SmartStream\Data;

use SplFileInfo;

class FileBucket extends DataBucket
{
    public const DISPOSITION_INLINE = 'inline';
    public const DISPOSITION_ATTACHMENT = 'attachment';

    public const TYPE_OCTET_STREAM = 'application/octet-stream';

    protected const IS_FORMATTABLE = false;
    protected ?string $contentType = null;
    protected ?string $contentDisposition = null;
    protected ?string $fileName = null;

    public static function createFromFile(string $filePath): self
    {
        $finfo = new SplFileInfo($filePath);
        $type = (function_exists('mime_content_type') && mime_content_type($filePath)) ?: null;
        return new static($finfo, $type, $finfo->getFilename());
    }

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
            case is_string($data):
            case is_resource($data):
            case $data instanceof SplFileInfo:
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

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;
        $this->setHeader('Content-Type', $contentType);
        return $this;
    }
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getContentDisposition(): ?string
    {
        return $this->contentDisposition;
    }
    public function setInline(): self
    {
        $this->contentDisposition = self::DISPOSITION_INLINE;
        $this->setDispositionHeader();
        return $this;
    }
    public function setAttachment(?string $filename): self
    {
        $this->contentDisposition = self::DISPOSITION_ATTACHMENT;
        $this->fileName = $filename;
        $this->setDispositionHeader();
        return $this;
    }
    private function setDispositionHeader(): void
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
        $this->setHeader('Content-Disposition', $headerBody);
    }
}
