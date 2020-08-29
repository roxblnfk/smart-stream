<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Matching;

use Generator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use roxblnfk\SmartStream\ConverterInterface;
use roxblnfk\SmartStream\ConverterMatcherInterface;
use roxblnfk\SmartStream\Data\DataBucket;
use roxblnfk\SmartStream\Exception\ConverterNotFoundException;
use Yiisoft\Http\Header;

final class SimpleConverterMatcher implements ConverterMatcherInterface
{
    private SimpleMatcherConfig $matcherConfig;
    private ContainerInterface $container;
    private ?RequestInterface $request = null;

    public function __construct(ContainerInterface $container, SimpleMatcherConfig $matcherConfig)
    {
        $this->container = $container;
        $this->matcherConfig = $matcherConfig;
    }

    public function match(DataBucket $bucket): ?MatchingResult
    {
        if (!$bucket->isConvertable()) {
            return null;
        }

        # find by format from bucket
        # find by bucket class name
        $format = $this->getBucketFormat($bucket) ?? $this->getBucketClassFormat($bucket);
        if ($format === null) {
            return null;
        }

        # multiple formats
        if (is_array($format)) {
            # if content-type specified in the bucket
            if (count($format) > 1 && $bucket->hasHeader(Header::CONTENT_TYPE)) {
                $format = $this->selectFormatByPreferredContentType($format, $bucket->getHeaderLine(Header::CONTENT_TYPE));
            }
            if (count($format) > 1) {
                # find format by headers
                $format = $this->compareWithAcceptTypes($format) ?? current($format);
            } else {
                $format = current($format);
            }
        }

        $converter = $this->container->get($this->matcherConfig->getConverter($format));
        return new MatchingResult($format, $converter, $this->matcherConfig->getMimeType($format));
    }
    public function withRequest(?RequestInterface $request): ConverterMatcherInterface
    {
        $new = clone $this;
        $new->request = $request;
        return $new;
    }

    private function getBucketFormat(DataBucket $bucket): ?string
    {
        if ($bucket->hasFormat()) {
            $format = $bucket->getFormat();
            if (!$this->matcherConfig->hasFormat($format)) {
                throw new ConverterNotFoundException($format);
            }
            return $format;
        }
        return null;
    }
    private function getBucketClassFormat(DataBucket $bucket): ?array
    {
        $className = static function (DataBucket $bucket): Generator {
            yield get_class($bucket);
            yield from class_parents($bucket);
        };
        $result = [];
        foreach ($className($bucket) as $bucketClass) {
            if ($this->matcherConfig->hasBucketFormat($bucketClass)) {
                $result = array_merge($result, $this->matcherConfig->getBucketFormats($bucketClass));
            }
        }
        return count($result) > 0 ? array_unique($result) : null;
    }
    private function selectFormatByPreferredContentType(array $formats, string $contentType): array
    {
        foreach ($formats as $format) {
            if ($this->matcherConfig->getMimeType($format) === $contentType) {
                # todo
            }
        }
        return $formats;
    }
    /**
     * @param string[] $format
     * @return null|string
     */
    private function compareWithAcceptTypes(array $format): ?string
    {
        return current($format);
        # TODO
        // $header = $this->request->getHeaderLine(Header::ACCEPT);
        // return null;
    }
}
