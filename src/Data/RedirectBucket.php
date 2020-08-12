<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Data;

use Yiisoft\Http\Header;
use Yiisoft\Http\Status;

class RedirectBucket extends DataBucket
{
    protected ?string $location = null;

    protected const IS_CONVERTABLE = false;

    public function __construct(string $location, int $code = Status::FOUND)
    {
        parent::__construct('');
        $this->setLocation($location);
        $this->setStatusCode($code);
    }
    public function getLocation(): ?string
    {
        return $this->location;
    }
    public function withLocation(?string $location): self
    {
        $clone = clone $this;
        $clone->setLocation($location);
        return $clone;
    }

    protected function setLocation(?string $location): void
    {
        $this->location = $location;
        $this->setHeader(Header::LOCATION, $this->location);
    }
}
