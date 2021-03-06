<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Data;

class WebTemplateBucket extends DataBucket
{
    private ?string $template = null;
    private ?string $layout = null;
    private iterable $layoutData = [];
    private iterable $commonData = [];

    public function __construct(iterable $templateData, string $format = null, array $params = [])
    {
        parent::__construct($templateData, $format, $params);
    }
    public function getTemplate(): ?string
    {
        return $this->template;
    }
    public function getCommonData(): iterable
    {
        return $this->commonData;
    }
    public function getLayout(): ?string
    {
        return $this->layout;
    }
    public function getLayoutData(): iterable
    {
        return $this->layoutData;
    }
    public function getTemplateData(): iterable
    {
        return $this->data;
    }

    public function withLayout(string $layout, iterable $layoutData = []): self
    {
        $new = clone $this;
        $new->layout = $layout;
        $new->layoutData = $layoutData;
        return $new;
    }
    public function withTemplate(string $template): self
    {
        $new = clone $this;
        $new->template = $template;
        return $new;
    }
    public function withCommonData(iterable $defaultData): self
    {
        $new = clone $this;
        $new->commonData = $defaultData;
        return $new;
    }
}
