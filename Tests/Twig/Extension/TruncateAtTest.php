<?php

namespace Widop\TwigExtensionsBundle\Tests\Twig\Extension;

require_once __DIR__ . '/../../../Twig/Extension/WidopTwigHelpersExtension.php';

use Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension as Ext;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Unit test class of the the widop twig bundle.
 *
 * @author Geoffrey Brier <geoffrey@widop.com>
 * @author Clément Herreman <clement@widop.com>
 */
class TruncateAtTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension The widop twig extension
     */
    private $twigExt;

    /**
     *  {@inheritdoc}
     */
    public function setUp()
    {
        $locale = 'en_US';
        $translator = new Translator($locale);
        $this->twigExt = new Ext($translator, $locale);
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     * @expectedException \InvalidArgumentException
     */
    public function testTruncateAtWithInvalidParams()
    {
        $this->assertEquals('', $this->twigExt->truncate_at('', -2));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
    */
    public function testTruncateAtWithLimitBiggerThanLength()
    {
        $this->assertEquals('the quick brown fox', $this->twigExt->truncate_at('the quick brown fox', 999));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
    */
    public function testTruncateAtIsWorkingWithTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $twig->addExtension($this->twigExt);
        $this->assertEquals('can i haz', $twig->render("{{ truncate_at('can i haz', 200) }}"));

    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at

    public function testTruncateAtDoesntCutSpaceSeparatedWord()
    {
        $this->assertEquals('the quick', $this->twigExt->truncate_at('the quick brown fox', 12));
        $this->assertEquals('the', $this->twigExt->truncate_at('the quick brown fox', 6));
        $this->assertEquals('the', $this->twigExt->truncate_at('the quick brown fox', 3));
    }*/

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTruncateAtDoesntRemovePunctuation()
    {
        //                                                              the quick. brown fox
        //                                                            13 is here ^
        $this->assertEquals('the quick.', $this->twigExt->truncate_at('the quick. brown fox', 13));
        $this->assertEquals('the quick,', $this->twigExt->truncate_at('the quick, brown fox', 13));
        $this->assertEquals('the quick;', $this->twigExt->truncate_at('the quick; brown fox', 13));
        $this->assertEquals('the quick', $this->twigExt->truncate_at('the quick (brown fox)', 13));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTrucateAtCutWord()
    {
        $this->assertEquals('the qui', $this->twigExt->truncate_at('the quick brown fox', 7, true));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTrucateAtTrimOriginalString()
    {
        $this->assertEquals('lorem',$this->twigExt->truncate_at('  lorem ipsum sid amet', 6));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTrucateAtTrimTruncatedString()
    {
        $this->assertEquals('lorem ipsum', $this->twigExt->truncate_at('lorem ipsum    ', 14));
    }
}
