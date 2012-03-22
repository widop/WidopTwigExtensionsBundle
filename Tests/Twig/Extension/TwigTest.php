<?php

namespace Widop\TwigExtensionsBundle\Tests\Twig\Extension;

require_once __DIR__ . '/../../../Twig/Extension/WidopTwigHelpersExtension.php';

use \Widop\TwigExtensionsBundle\Twig\Extension as Ext;

/**
 * Unit test class of the the widop twig bundle.
 *
 * @author Geoffrey Brier <geoffrey@widop.com>
 * @author Clément Herreman <clement@widop.com>
 */
class TwigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     * @expectedException InvalidArgumentException
    */
    public function testTruncateAfterWithInvalidParams()
    {
        $this->assertEquals('', Ext\truncate_at('', -1, false));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     */
    public function testTruncateWithLimitBiggerThanLength()
    {
        $this->assertEquals('the quick brown fox', Ext\truncate_at('the quick brown fox', 999));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     */
    public function testTruncateDoesntCutSpaceSeparatedWord()
    {
        $this->assertEquals('the quick', Ext\truncate_at('the quick brown fox', 12));
        $this->assertEquals('the', Ext\truncate_at('the quick brown fox', 6));
        $this->assertEquals('the', Ext\truncate_at('the quick brown fox', 3));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     */
    public function testTruncateDoesntRemovePunctuation()
    {
        //                                                    the quick. brown fox
        //                                                     13 is here ^
        $this->assertEquals('the quick.', Ext\truncate_at('the quick. brown fox', 13));
        $this->assertEquals('the quick,', Ext\truncate_at('the quick, brown fox', 13));
        $this->assertEquals('the quick;', Ext\truncate_at('the quick; brown fox', 13));
        $this->assertEquals('the quick', Ext\truncate_at('the quick (brown fox)', 13));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     */
    public function testTrucateCutWord()
    {
        $this->assertEquals('the qui', Ext\truncate_at('the quick brown fox', 7, true));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     */
    public function testTrucateTrimOriginalString()
    {
        $this->assertEquals('lorem', Ext\truncate_at('  lorem ipsum sid amet', 6));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_at()
     */
    public function testTrucateTrimTruncatedString()
    {
        $this->assertEquals('lorem ipsum', Ext\truncate_at('lorem ipsum    ', 14));
    }
}
