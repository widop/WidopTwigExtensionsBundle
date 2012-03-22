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
 */
class DateIntervalTest extends \PHPUnit_Framework_TestCase
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
        $locale = 'en';
        $translator = new Translator($locale);
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource(
            'yaml',
            __DIR__.'/../../../Resources/translations/messages.en.yml',
            $locale
        );

        $this->twigExt = new Ext($translator, $locale);
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalIsWorkingWithTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $twig->addExtension($this->twigExt);
        $this->assertEquals('A few seconds ago', $twig->render("{{ date_interval() }}"));
        $this->assertEquals('A few seconds ago', $twig->render("{{ 'now' | date_interval }}"));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalWithDateTime()
    {
        $this->assertEquals('1 minute ago', $this->twigExt->date_interval(new \DateTime('-1minutes')));
        $this->assertEquals('2 minutes ago', $this->twigExt->date_interval(new \DateTime('-2minutes')));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalMinutes()
    {
        $this->assertEquals('1 minute ago', $this->twigExt->date_interval('-1minutes'));
        $this->assertEquals('2 minutes ago', $this->twigExt->date_interval('-2minutes'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalHours()
    {
        $this->assertEquals('1 hour ago', $this->twigExt->date_interval('-1hours'));
        $this->assertEquals('2 hours ago', $this->twigExt->date_interval('-2hours'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalDays()
    {
        $this->assertEquals('1 day ago', $this->twigExt->date_interval('-1days'));
        $this->assertEquals('2 days ago', $this->twigExt->date_interval('-2days'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalMonths()
    {
        $this->assertEquals('1 month ago', $this->twigExt->date_interval('-1months'));
        $this->assertEquals('2 months ago', $this->twigExt->date_interval('-2months'));
    }

    /**
     * @covers \Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension::date_interval
     */
    public function testDateIntervalYears()
    {
        $this->assertEquals('1 year ago', $this->twigExt->date_interval('-1years'));
        $this->assertEquals('2 years ago', $this->twigExt->date_interval('-2years'));
    }
}
