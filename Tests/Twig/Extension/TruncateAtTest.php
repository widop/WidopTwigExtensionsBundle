<?php

/*
 * This file is part of the Widop package.
 *
 * (c) Widop <contact@widop.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Widop\TwigExtensionsBundle\Tests\Twig\Extension;

require_once __DIR__ . '/../../../Twig/Extension/WidopTwigHelpersExtension.php';

use Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension;

use Symfony\Component\Translation\Translator,
    Symfony\Component\Translation\MessageSelector,
    Symfony\Component\Translation\Loader\YamlFileLoader;

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
    private $twigExtension;

    /**
     *  {@inheritdoc}
     */
    public function setUp()
    {
        $locale = 'en';
        $translator = new Translator($locale);

        $this->twigExtension = new WidopTwigHelpersExtension($translator, $locale);
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     *
     * @expectedException \InvalidArgumentException
     */
    public function testTruncateAtWithInvalidParams()
    {
        $this->assertEquals('', $this->twigExtension->truncate_at('', -2));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTruncateAtWithLimitBiggerThanLength()
    {
        $this->assertEquals('the quick brown fox', $this->twigExtension->truncate_at('the quick brown fox', 999));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTruncateAtIsWorkingWithTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $twig->addExtension($this->twigExtension);
        $this->assertEquals('can i haz', $twig->render("{{ truncate_at('can i haz', 200) }}"));
        $this->assertEquals('can i haz', $twig->render("{{ 'can i haz' | truncate_at(200) }}"));

    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTruncateAtDoesntCutSpaceSeparatedWord()
    {
        $this->assertEquals('the quick', $this->twigExtension->truncate_at('the quick brown fox', 12));
        $this->assertEquals('the', $this->twigExtension->truncate_at('the quick brown fox', 6));
        $this->assertEquals('the', $this->twigExtension->truncate_at('the quick brown fox', 3));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTruncateAtDoesntRemovePunctuation()
    {
        //                                                              the quick. brown fox
        //                                                            13 is here ^
        $this->assertEquals('the quick.', $this->twigExtension->truncate_at('the quick. brown fox', 13));
        $this->assertEquals('the quick,', $this->twigExtension->truncate_at('the quick, brown fox', 13));
        $this->assertEquals('the quick;', $this->twigExtension->truncate_at('the quick; brown fox', 13));
        $this->assertEquals('the quick', $this->twigExtension->truncate_at('the quick (brown fox)', 13));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTrucateAtCutWord()
    {
        $this->assertEquals('the qui', $this->twigExtension->truncate_at('the quick brown fox', 7, true));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTrucateAtTrimOriginalString()
    {
        $this->assertEquals('lorem', $this->twigExtension->truncate_at('  lorem ipsum sid amet', 6));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::truncate_at
     */
    public function testTrucateAtTrimTruncatedString()
    {
        $this->assertEquals('lorem ipsum', $this->twigExtension->truncate_at('lorem ipsum    ', 14));
    }
}
