<?php

namespace roxblnfk\SmartStream\Data;

use InvalidArgumentException;
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
     * @param null|string $fileName
     * @throws \Exception
     */
    public function __construct($data, string $contentType = null, string $fileName = null)
    {
        switch (true) {
            case $data instanceof SplFileInfo:
                if (!$data->isFile()) {
                    throw new InvalidArgumentException('File does not exist or is not a file.');
                }
                $fileName = $fileName ?? $data->getFilename();
                // $contentType = $contentType ?? $this->fileContentType($data->getPathname());
                parent::__construct($data);
                break;
            case is_string($data):
            case is_resource($data):
                parent::__construct($data);
                break;
            default:
                throw new InvalidArgumentException(
                    'The $data parameter must be a resource, a string or an instance of SplFileInfo.'
                );
        }

        if ($contentType !== null) {
            $this->setContentType($contentType);
        }
        if ($fileName !== null) {
            $this->setAttachment($fileName);
        }
    }

    public static function createFromPath(string $filePath, string $contentType = null, string $fileName = null): self
    {
        return new static(new SplFileInfo($filePath), $contentType, $fileName);
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
    public function withoutDisposition(): self
    {
        $clone = clone $this;
        $clone->contentDisposition = null;
        $clone->setDispositionHeader();
        return $clone;
    }
    public function withAutoContentType(): self
    {
        $clone = clone $this;
        $type = null;
        if ($clone->data instanceof SplFileInfo) {
            $type = $clone->fileContentType($clone->data->getPathname());
        } elseif (is_string($clone->data)) {
            $type = $clone->bufferContentType($clone->data);
        }
        $clone->setContentType($type);
        return $clone;
    }

    protected function setContentType(?string $contentType): void
    {
        $this->contentType = $contentType;
        $this->setHeader(Header::CONTENT_TYPE, $contentType);
    }
    protected function setInline(): void
    {
        $this->contentDisposition = self::DISPOSITION_INLINE;
        $this->setDispositionHeader();
    }
    final protected function setAttachment(?string $fileName): void
    {
        $this->contentDisposition = self::DISPOSITION_ATTACHMENT;
        $this->fileName = $fileName;
        $this->setDispositionHeader();
    }
    final protected function setDispositionHeader(): void
    {
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
    private function fileContentType(string $filePath): ?string
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return null;
        }
        return function_exists('mime_content_type') ? mime_content_type($filePath) : null;
    }
    private function bufferContentType(string $buffer): ?string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $result = finfo_buffer($finfo, $buffer);
            if (is_string($result)) {
                return $result;
            }
        }
        return null;
    }
}
