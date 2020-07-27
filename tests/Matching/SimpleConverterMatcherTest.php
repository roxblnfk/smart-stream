<?php

declare(strict_types=1);

namespace roxblnfk\SmartStream\Tests\Matching;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use roxblnfk\SmartStream\Matching\SimpleConverterMatcher;
use roxblnfk\SmartStream\Matching\SimpleMatcherConfig;

class SimpleConverterMatcherTest extends TestCase
{
    # Immutability

    public function testWithRequestImmutability(): void
    {
        $matcher = $this->createMatcher();

        $newMatcher = $matcher->withRequest($this->createMock(ServerRequestInterface::class));

        $this->assertNotSame($matcher, $newMatcher);
    }

    private function createContainer(): ContainerInterface
    {
        return $this->createMock(ContainerInterface::class);
    }
    private function createConfig(): SimpleMatcherConfig
    {
        return new SimpleMatcherConfig();
    }
    private function createMatcher(SimpleMatcherConfig $config = null): SimpleConverterMatcher
    {
        return new SimpleConverterMatcher($this->createContainer(), $config ?? $this->createConfig());
    }
}
