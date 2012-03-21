<?php

namespace Widop\TwigExtensionsBundle\Tests\Twig\Extension;

require_once __DIR__ . '/../../../Twig/Extension/WidopTwigHelpersExtension.php';

use \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after;

/**
 * Unit test class of the the widop twig bundle.
 *
 * @todo Check how to really call truncate_after function
 * @author Geoffrey Brier <geoffrey@widop.com>
 */
class TwigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array Array of strings used by the tests
     */
    protected $dataset;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->dataset = array('ab', ' cd e', 'f');
    }

    /**
     *  @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after()
     */
    public function testTruncateAfterWithInvalidParams()
    {
        // empty array
        $this->assertEquals('', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after(array(), 20, false));
        // Invalid length
        $this->assertEquals('ab cd e f', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 20, false));
        $this->assertEquals('', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, -1, false));
    }

    /**
     *  @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after()
     */
    public function testTruncateAfterWithSpecificOffsets()
    {
        // In the middle of a word (first string offset)
        $this->assertEquals('ab', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 1, false));
        $this->assertEquals('a', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 1, true));
        // In the end of an offset (first string offset)
        $this->assertEquals('ab', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 2, false));
        $this->assertEquals('ab', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 2, true));
        // In the middle of a word (second string offset)
        $this->assertEquals('ab cd', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 3, false));
        $this->assertEquals('ab', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 3, true));
        // In the end of a word (second string offset)
        $this->assertEquals('ab cd', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 4, false));
        $this->assertEquals('ab c', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 4, true));
        // On a space (second string offset)
        $this->assertEquals('ab cd', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 5, false));
        $this->assertEquals('ab cd', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($this->dataset, 5, true));
    }

    /**
     *  @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after()
     */
    public function testTruncateAfterWithWeirdParams()
    {
        $weirdDataset = array('ab', ' cd e   ',  ' f      f ', '        ', "\t", 'a');
        $this->assertEquals('ab cd e f      f a', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($weirdDataset, 200, false));
    }
}