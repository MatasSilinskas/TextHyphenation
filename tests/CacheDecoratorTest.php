<?php

namespace TextHyphenation\Tests;


use PHPUnit\Framework\TestCase;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Hyphenators\Cache;
use TextHyphenation\Hyphenators\Hyphenator;

class CacheDecoratorTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \TextHyphenation\Cache\InvalidArgumentException
     */
    public function testHyphenate(): void
    {
        $hyphenator = $this->createMock(Hyphenator::class);
        $hyphenator
            ->expects($this->once())
            ->method('hyphenate')
            ->with('word')
            ->willReturn('nonCachedValue');

        $cachePool = $this->createMock(ArrayCachePool::class);
        $cachePool
            ->expects($this->once())
            ->method('get')
            ->willReturn('cachedValue');

        $cachePool
            ->expects($this->once())
            ->method('set');

        $cachePool
            ->method('has')
            ->willReturnOnConsecutiveCalls(false, true);

        $cache = new Cache($hyphenator, $cachePool);

        $this->assertSame('nonCachedValue', $cache->hyphenate('word'));
        $this->assertSame('cachedValue', $cache->hyphenate('word'));
    }
}