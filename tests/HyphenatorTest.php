<?php

namespace TextHyphenation\Tests;

use PHPUnit\Framework\TestCase;
use TextHyphenation\Hyphenators\Hyphenator;

class HyphenatorTest extends TestCase
{
    private $patterns = [];
    private $extraPatterns = ['2pppppp2', '1xdxd2'];

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->patterns = array_unique(array_reduce(array_column($this->hyphenationProvider(), 2), 'array_merge', []));
        $this->patterns = array_merge($this->patterns, $this->extraPatterns);
    }

    /**
     * @param string $word
     * @param string $expected
     * @param array $expectedPatterns
     * @throws \Exception
     *
     * @dataProvider hyphenationProvider
     */
    public function testHyphenation(string $word, string $expected, array $expectedPatterns): void
    {
        $hyphenator = new Hyphenator($this->patterns);
        $actualPatterns = [];
        $this->assertEquals($expected, $hyphenator->hyphenate($word, $actualPatterns));
        $this->assertEquals($expectedPatterns, $actualPatterns);
    }

    /**
     * @return array
     */
    public function hyphenationProvider(): array
    {
        return [
            ['mistranslate', 'mis-trans-late', ['s3lat', '.mis1']],
            ['alphabetical', 'al-pha-bet-i-cal', ['1ca', 'et1ic', '3bet', 'l3pha']],
            ['bewildering', 'be-wil-der-ing', ['er1i', 'ewil5', 'be3w']],
            ['buttons', 'but-ton-s', ['on3s', 't5to']],
            ['ceremony', 'cer-e-mo-ny', ['mo3ny.', '1mo', 'er3emo']],
            ['hovercraft', 'hov-er-craft', ['r1c', '.hov5']],
            ['lexicographically', 'lex-i-co-graph-i-cal-ly', ['l1l', '1ca', 'ph1ic', '5graphic', 'xi5c', 'x3i']],
            ['programmer', 'pro-gram-mer', ['m1m', '1gr']],
            ['recursion', 're-cur-sion', ['5sion', 'e1cu']],
            ['aa', 'aa', []],
            ['maaaaaaamaaaaaaamaaaaaaa', 'maaaaaaa-maaaaaaa-maaaaaaa', ['1maaaaaaa']],
            ['mismismis', 'mis-mismis', ['.mis1']],
            ['monymonymony', 'mony-mony-mo-ny', ['mo3ny.', '1mo']],
            ['bbbccccccbbb', 'bbb-cccccc-bbb', ['1cccccc1']],
            ['bbbppppppbbb', 'bbbppppppbbb', []],
            ['xdxdxdxdxdxd', 'xdxdxdxdxdxd', []],
        ];
    }
}