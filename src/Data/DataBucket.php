<?php

namespace roxblnfk\SmartStream\Data;

use Yiisoft\Http\Status;

class DataBucket
{
    /** @var mixed */
    protected $data;
    protected ?int $statusCode = null;
    /** @var string[] */
    protected array $headers = [];
    protected ?string $format = null;
    protected iterable $params = [];

    protected const IS_CONVERTABLE = true;

    public function __construct($data, string $format = null, iterable $params = [])
    {
        $this->data = $data;
        if ($format !== null) {
            $this->format = $format;
            $this->params = $params;
        }
    }
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
    public function getFormat(): ?string
    {
        return $this->format;
    }
    public function getParams(): iterable
    {
        return $this->params;
    }
    public function getData()
    {
        return $this->data;
    }
    public function isConvertable(): bool
    {
        return static::IS_CONVERTABLE;
    }
    public function hasFormat(): bool
    {
        return static::IS_CONVERTABLE && $this->format !== null;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }
    public function getHeaderLine(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withStatusCode(?int $code = Status::OK): self
    {
        $new = clone $this;
        $new->setStatusCode($code);
        return $new;
    }
    public function withHeader(string $name, string $value): self
    {
        $new = clone $this;
        $new->setHeader($name, $value);
        return $new;
    }
    public function withFormat(?string $format, array $params = null): self
    {
        $new = clone $this;
        $new->setFormat($format, $params);
        return $new;
    }
    public function withoutHeader(string $name): self
    {
        $new = clone $this;
        $new->unsetHeader($name);
        return $new;
    }
    public function withoutHeaders(): self
    {
        $new = clone $this;
        $new->headers = [];
        return $new;
    }

    protected function unsetHeader(string $name): void
    {
        unset($this->headers[$name]);
    }
    protected function setHeader(string $name, ?string $value): void
    {
        if ($value === null) {
            $this->unsetHeader($name);
        } else {
            $this->headers[$name] = $value;
        }
    }
    protected function setFormat(?string $format, ?iterable $params): void
    {
        $this->format = $format;
        if ($params !== null) {
            $this->params = $params;
        }
    }
    protected function setStatusCode(?int $code): void
    {
        $this->statusCode = $code;
    }
}
