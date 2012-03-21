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
     * Data provider.
     *
     * @return array
     */
    static public function provider()
    {
        return array(
            array('ab c', 1, false, ''),
            array('ab c', 1, true, 'a'),
            array('ab c', 2, false, 'ab'),
            array('ab c', 3, false, 'ab'),
            array('ab c', 4, false, 'ab c'),
            array('ab c', 5, false, 'ab c'),
            array('ab      c', 5, false, 'ab'),
            array('       c', 1, false, 'c'),
            array('       c', 2, false, 'c'),
            array('       c', 5, false, 'c'),
            array('abcde', 5, false, 'abcde'),
            array('abcde ', 5, false, 'abcde'),
            array('abcde ', 6, false, 'abcde'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after()
     * @expectedException InvalidArgumentException
    */
    public function testTruncateAfterWithInvalidParams()
    {
        $this->assertEquals('', \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after('', -1, false));
    }

    /**
     * @dataProvider provider
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after()
     */
    public function testTruncateAfterWithSpecificOffsets($testedString, $offset, $doCutWord, $expectedReturn)
    {
        $this->assertEquals($expectedReturn, \Widop\TwigExtensionsBundle\Twig\Extension\truncate_after($testedString, $offset, $doCutWord));
    }
}