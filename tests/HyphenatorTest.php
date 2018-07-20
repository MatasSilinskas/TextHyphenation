<?php

namespace TextHyphenation\Tests;

use PHPUnit\Framework\TestCase;
use TextHyphenation\DataProviders\ProviderFactory;
use TextHyphenation\Hyphenators\Hyphenator;

class HyphenatorTest extends TestCase
{
    /**
     * @param string $word
     * @param string $expected
     * @throws \Exception
     *
     * @dataProvider hyphenationProvider
     */
    public function testHyphenate(string $word, string $expected): void
    {
        $factory = new ProviderFactory();
        $patternsProvider = $factory->createProvider('patterns');
        $hyphenator = new Hyphenator($patternsProvider->getData());
        $this->assertEquals($expected, $hyphenator->hyphenate($word));
    }

    public function hyphenationProvider(): array
    {
        return [
            ['mistranslate', 'mis-trans-late'],
            ['alphabetical', 'al-pha-bet-i-cal'],
            ['bewildering', 'be-wil-der-ing'],
            ['buttons', 'but-ton-s'],
            ['ceremony', 'cer-e-mo-ny'],
            ['hovercraft', 'hov-er-craft'],
            ['lexicographically', 'lex-i-co-graph-i-cal-ly'],
            ['programmer', 'pro-gram-mer'],
            ['recursion', 're-cur-sion'],
            ['aa', 'aa'],
        ];
    }
}