<?php

namespace Widop\TwigExtensionsBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class declares custom TWIG filters and functions.
 */
class WidopTwigHelpersExtension extends \Twig_Extension
{

    protected $translator;
    
    /**
     * Constructor method
     * This constructor needs a translator (and the locale for the moment).
     * 
     * @param TranslatorInterface $translator The translator
     * @param $locale The variable %locale%
     */
    public function __construct(TranslatorInterface $translator, $locale)
    {
        $this->setTranslator($translator);
        // @TODO change this
        // Hack to force locale being correctly set
        $this->translator->setLocale($locale);
    }
    
    /**
     * Set a translator
     * 
     * @param TranslatorInterface $translator A translator
     * 
     * @return WidopTwigHelpersExtension
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'date_interval' => new \Twig_Filter_Method($this, 'format_date_interval', array('is_safe' => array('html'))),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'date_interval' => new \Twig_Function_Method($this, 'format_date_interval', array('is_safe' => array('html'))),
        );
    }
    
    /**
     * Return a nice date output, very similar to a countdown.
     * This method works both as a filter and a method, and handles I18N and P10N.
     * 
     * Examples:
     * {{ date('-2days') | date_interval }} --> 2 days ago
     * {{ date_interval('now') }} <==> {{ date_interval() }} --> A few moments ago
     * {{ date_interval(date('-1years')) }} --> A year ago
     * 
     * NB: If the $date parameter is null, the method will try to convert it
     * into a datetime. If no parameter is passed, the method will take 'now' as
     * as a default date.
     * 
     * @param $date Either a Datetime or a string (which can be turned into a Datetime)
     * 
     * @return A string similar to a countdown
     */
    public function format_date_interval($date = null)
    {
        $now = new \DateTime('now');
        $interval = $now->diff(($date instanceof \DateTime)? $date : new \DateTime($date));

        if ($interval->y !== 0) {
            $intervalNumber = $interval->y;
            $moment = 'wteb.year';
        } else if ($interval->m !== 0) {
            $intervalNumber = $interval->m;
            $moment = 'wteb.month';
        } else if ($interval->d !== 0) {
            $intervalNumber = $interval->d;
            $moment = 'wteb.day';
        } else if ($interval->h !== 0) {
            $intervalNumber = $interval->h;
            $moment = 'wteb.hour';
        } else if ($interval->i !== 0) {
            $intervalNumber = $interval->i;
            $moment = 'wteb.minute';
        } else {
            return $this->translator->trans('wteb.second');
        }
        
        $moment = $this->translator->transChoice($moment, $intervalNumber);
        return $this->translator->trans('%nb% %moment%',
                array('%nb%' => $intervalNumber, '%moment%' => $moment));
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'widop_twig_helpers';
    }
}
