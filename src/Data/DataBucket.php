<?php

namespace roxblnfk\SmartStream\Data;

class DataBucket
{
    /** @var mixed */
    protected $data;
    protected ?int $code = null;
    protected array $headers = [];
    protected ?string $format = null;
    protected array $params = [];

    public function __construct($data, string $format = null, array $params = [])
    {
        $this->data = $data;
        $this->format = $format;
    }
    public function getCode(): ?int
    {
        return $this->code;
    }
    public function getHeaders(): array
    {
        return $this->headers;
    }
    public function getFormat(): ?string
    {
        return $this->format;
    }
    public function getParams(): array
    {
        return $this->params;
    }
    public function getData()
    {
        return $this->data;
    }

    public function setCode(?int $code = 200): self
    {
        $this->code = $code;
        return $this;
    }
    public function setHeaders(array $headers = []): self
    {
        $this->headers = $headers;
        return $this;
    }
    public function addHeaders(array $headers = []): self
    {
        $this->headers = [...$this->headers, ...$headers];
        return $this;
    }
    public function setFormat(?string $format, array $params = null): self
    {
        $this->format = $format;
        if ($params !== null) {
            $this->params = $params;
        }
        return $this;
    }
}
