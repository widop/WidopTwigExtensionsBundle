<?php

namespace Widop\TwigExtensionsBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class declares custom TWIG filters and functions.
 *
 * @author Geoffrey Brier <geoffrey@widop.com>
 */
class WidopTwigHelpersExtension extends \Twig_Extension
{
    /**
     * @var Symfony\Component\Translation\TranslatorInterface A translator.
     */
    protected $translator;

    /**
     * The constructor needs a translator (and the locale for the moment).
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator The translator.
     * @param string                                             $locale     The variable %locale%.
     */
    public function __construct(TranslatorInterface $translator, $locale)
    {
        $this->setTranslator($translator);
        // @TODO change this
        // Hack to force locale being correctly set
        $this->translator->setLocale($locale);
    }

    /**
     * Set a translator.
     *
     * @param Symfony\Component\Translation\TranslatorInterface $translator A translator.
     *
     * @return Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension
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
            'date_interval' => new \Twig_Filter_Method('format_date_interval', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'date_interval' => new \Twig_Function_Method('format_date_interval', array('is_safe' => array('html'))),
            'truncate_after' => new \Twig_Function_Method('truncate_after', array('is_safe' => array('html'))),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'widop_twig_helpers';
    }
}

/**
 * Properly concat an array of strings after a certain limit. Strings are also
 * trimmed and joined together with a space if possible.
 *
 * NB: Given the $cutWord parameter, the behaviour of the method may change.
 * Examples:
 *  $d = array('ab', ' cd e', 'f');
 *  truncate_after($d, 2, false) --> 'ab'
 *  truncate_after($d, 4, false) --> 'ab cd' // Don't forget, strings are joined using a ' '
 *  truncate_after($d, 4, true)  --> 'ab c'
 *  //!\\ //!\\ //!\\ //!\\
 *  truncate_after($d, 3, false) --> 'ab cd' // Look only for any non word char after the space (that's why the 'e' is taken)
 *  truncate_after($d, 3, true)  --> 'ab'
 *
 * @param array   $strings  An array of strings.
 * @param integer $limit    Limit where to cut
 * @param boolean $cutWords Do we cut words or not (optionnal).
 *
 * @return string
 */
function truncate_after(array $strings, $limit, $cutWords = false)
{
    if ($limit < 0) {
        return '';
    }

    $truncatedString = '';
    for ($i = 0 ; $i < count($strings) ; $i++) {
        $tLen = strlen($truncatedString);
        // Join words with a space
        $strings[$i] = trim($strings[$i]);
        if ($i > 0 && strlen($strings[$i])) {
            $strings[$i] = ' ' . $strings[$i];
        }

        // Fully concat words if i still can
        if (($tLen + strlen($strings[$i])) - $limit <= 0) {
            $truncatedString .= $strings[$i];
        } else {
            // Find offset where to cut
            $offset = $limit - ($tLen + strlen($strings[$i]));
            if (!$cutWords) {
                if (preg_match('/(\W)/', substr($strings[$i], $offset), $matches, PREG_OFFSET_CAPTURE)) {
                    $offset += $matches[1][1];
                } else {
                    $offset = strlen($strings[$i]);
                }
            }
            $truncatedString .= substr($strings[$i], 0, $offset);
            break;
        }
    }

    return trim($truncatedString, ' ');
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
    * @param string|\DateTime $date Either a Datetime or a string (which can be turned into a Datetime).
    *
    * @return string
    */
function format_date_interval($date = null)
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

    return $this->translator->trans('%nb% %moment%', array(
        '%nb%'      => $intervalNumber,
        '%moment%'  => $moment
    ));
}
