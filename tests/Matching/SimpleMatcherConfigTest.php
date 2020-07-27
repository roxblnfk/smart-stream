<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Matching;

use PHPUnit\Framework\TestCase;
use roxblnfk\SmartStream\Matching\SimpleMatcherConfig;
use roxblnfk\SmartStream\Tests\Support\DummyConverter;

class SimpleMatcherConfigTest extends TestCase
{
    # Immutability

    public function testWithFormatImmutability(): void
    {
        $config = $this->createConfig();
        $format = 'test-format';

        $newConfig = $config->withFormat($format, DummyConverter::class);

        $this->assertNotSame($config, $newConfig);
        $this->assertFalse($config->hasFormat($format));
        $this->assertTrue($newConfig->hasFormat($format));
    }

    private function createConfig(): SimpleMatcherConfig
    {
        return new SimpleMatcherConfig();
    }
}
