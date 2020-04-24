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
    public function setLayout(string $layout, array $layoutData = []): self
    {
        $this->layout = $layout;
        $this->layoutData = $layoutData;
        return $this;
    }
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }
    public function addTemplateData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }
    public function setDefaultData(array $defaultData): self
    {
        $this->defaultData = $defaultData;
        return $this;
    }
    public function addDefaultData(string $key, $value): self
    {
        $this->defaultData[$key] = $value;
        return $this;
    }
}
