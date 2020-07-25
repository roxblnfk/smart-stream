<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Matching;

final class SimpleMatcherConfig
{
    /** @var array of array< 0: converter class; 1: mime type; 2: array of bucket class > */
    private array $formats = [];
    /** @var string[][] */
    private array $buckets = [];

    public function withFormat(string $format, string $converter, string $mimeType = null, array $buckets = []): self
    {
        $clone = clone $this;

        if (array_key_exists($format, $clone->formats)) {
            # remove format from buckets array
            array_walk($clone->buckets, static function (array &$formats) use ($format) {
                $formats = array_diff($formats, [$format]);
            });
        }

        $clone->formats[$format] = [$converter, $mimeType, $buckets];

        foreach ($buckets as $bucket) {
            if (!is_string($bucket)) {
                throw new \InvalidArgumentException('Bucket should be string value.');
            }
            if (!array_key_exists($bucket, $clone->buckets)) {
                $clone->buckets[$bucket] = [];
            }
            $clone->buckets[$bucket][] = [$format];
        }
        return $clone;
    }

    public function hasFormat(string $format): bool
    {
        return array_key_exists($format, $this->formats);
    }
    public function getConverter(string $format): string
    {
        return $this->formats[$format][0];
    }
    public function getMimeType(string $format): ?string
    {
        return $this->formats[$format][1];
    }
    public function hasBucketFormat(string $bucket): bool
    {
        return array_key_exists($bucket, $this->buckets);
    }
    public function getBucketFormats(string $bucket): array
    {
        return $this->buckets[$bucket];
    }
}
