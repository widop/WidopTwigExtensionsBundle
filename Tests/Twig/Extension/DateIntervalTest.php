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
 */
class DateIntervalTest extends \PHPUnit_Framework_TestCase
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
        $translator->addLoader('yaml', new YamlFileLoader());

        $ressource = __DIR__.'/../../../Resources/translations/messages.en.yml';
        $translator->addResource('yaml', $ressource, $locale);

        $this->twigExtension = new WidopTwigHelpersExtension($translator, $locale);
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalIsWorkingWithTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $twig->addExtension($this->twigExtension);
        $this->assertEquals('A few seconds ago', $twig->render("{{ date_interval() }}"));
        $this->assertEquals('A few seconds ago', $twig->render("{{ 'now' | date_interval }}"));
        $this->assertEquals('In 2 days', $twig->render("{{ date('2days') | date_interval }}"));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalWithDateTime()
    {
        $this->assertEquals('1 minute ago', $this->twigExtension->date_interval(new \DateTime('-1minutes')));
        $this->assertEquals('2 minutes ago', $this->twigExtension->date_interval(new \DateTime('-2minutes')));
        $this->assertEquals('In 2 minutes', $this->twigExtension->date_interval(new \DateTime('2minutes')));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalMinutes()
    {
        $this->assertEquals('1 minute ago', $this->twigExtension->date_interval('-1minutes'));
        $this->assertEquals('2 minutes ago', $this->twigExtension->date_interval('-2minutes'));
        $this->assertEquals('In 2 minutes', $this->twigExtension->date_interval('2minutes'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalHours()
    {
        $this->assertEquals('1 hour ago', $this->twigExtension->date_interval('-1hours'));
        $this->assertEquals('2 hours ago', $this->twigExtension->date_interval('-2hours'));
        $this->assertEquals('In 2 hours', $this->twigExtension->date_interval('2hours'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalDays()
    {
        $this->assertEquals('1 day ago', $this->twigExtension->date_interval('-1days'));
        $this->assertEquals('2 days ago', $this->twigExtension->date_interval('-2days'));
        $this->assertEquals('In 2 days', $this->twigExtension->date_interval('2days'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalMonths()
    {
        $this->assertEquals('1 month ago', $this->twigExtension->date_interval('-1months'));
        $this->assertEquals('2 months ago', $this->twigExtension->date_interval('-2months'));
        $this->assertEquals('In 2 months', $this->twigExtension->date_interval('2months'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalYears()
    {
        $this->assertEquals('1 year ago', $this->twigExtension->date_interval('-1years'));
        $this->assertEquals('2 years ago', $this->twigExtension->date_interval('-2years'));
        $this->assertEquals('In 2 years', $this->twigExtension->date_interval('2years'));
    }
}
