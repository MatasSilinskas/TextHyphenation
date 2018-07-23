<?php

namespace TextHyphenation\Tests;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TextHyphenation\Cache\ArrayCachePool;
use TextHyphenation\Hyphenators\Cache;
use TextHyphenation\Hyphenators\Hyphenator;

class CacheDecoratorTest extends TestCase
{
    /* @var Hyphenator|MockObject */
    private $hyphenator;
    /* @var ArrayCachePool|MockObject */
    private $cache;
    /* @var Cache */
    private $cachingHyphenator;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->hyphenator = $this->createMock(Hyphenator::class);
        $this->cache = $this->createMock(ArrayCachePool::class);

        $this->cachingHyphenator = new Cache($this->hyphenator, $this->cache);
    }

    /**
     * @throws \Exception
     */
    public function testHyphenate(): void
    {
        $this->hyphenator
            ->expects($this->once())
            ->method('hyphenate')
            ->with('word')
            ->willReturn('nonCachedValue');

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturn('cachedValue');

        $this->cache
            ->expects($this->once())
            ->method('set');

        $this->cache
            ->method('has')
            ->willReturnOnConsecutiveCalls(false, true);

        $this->assertSame('nonCachedValue', $this->cachingHyphenator->hyphenate('word'));
        $this->assertSame('cachedValue', $this->cachingHyphenator->hyphenate('word'));
    }
}