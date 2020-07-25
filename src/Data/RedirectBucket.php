<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Data;

class RedirectBucket extends DataBucket
{
    protected ?string $location = null;

    protected const IS_FORMATABLE = false;

    public function __construct(string $location, int $code = 302)
    {
        parent::__construct('');
        $this->setLocation($location);
        $this->setCode($code);
    }
    public function getLocation(): ?string
    {
        return $this->location;
    }
    public function setLocation(?string $location): self
    {
        $this->location = $location;
        $this->setHeader('Location', $this->location);
        return $this;
    }
}
