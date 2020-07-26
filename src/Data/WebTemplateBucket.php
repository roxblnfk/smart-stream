<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Data;

class WebTemplateBucket extends DataBucket
{
    private ?string $template = null;
    private ?string $layout = null;
    private array $layoutData = [];
    private array $defaultData = [];

    public function getTemplate(): ?string
    {
        return $this->template;
    }
    public function getDefaultData(): array
    {
        return $this->defaultData;
    }
    public function getLayout(): ?string
    {
        return $this->layout;
    }
    public function getLayoutData(): array
    {
        return $this->layoutData;
    }
    public function getTemplateData(): array
    {
        return $this->data;
    }

    public function withLayout(string $layout, array $layoutData = []): self
    {
        $clone = clone $this;
        $clone->layout = $layout;
        $clone->layoutData = $layoutData;
        return $clone;
    }
    public function withTemplate(string $template): self
    {
        $clone = clone $this;
        $clone->template = $template;
        return $clone;
    }
    public function withAddedTemplateData(string $key, $value): self
    {
        $clone = clone $this;
        $clone->data[$key] = $value;
        return $clone;
    }
    public function withDefaultData(array $defaultData): self
    {
        $clone = clone $this;
        $clone->defaultData = $defaultData;
        return $clone;
    }
    public function withAddedDefaultData(string $key, $value): self
    {
        $clone = clone $this;
        $clone->defaultData[$key] = $value;
        return $clone;
    }
}
