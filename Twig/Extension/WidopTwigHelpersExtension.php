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
 * Specially truncate a string given a limit.
 *
 * If this limit is higher than the length of the string, then the limit is
 * changed to this length. If this limit is inferior or equal to 0, then an
 * exception is thrown.
 *
 * NB: The string is first trimmed.
 *
 * Given the $cutWord parameter, the behaviour of the method may change.
 * Examples:
 *   truncate_after('ab c', 1, false)      --> ''
 *   truncate_after('ab c', 1, true)       --> 'a'
 *   truncate_after('ab c', 2, false)      --> 'ab'
 *   truncate_after('ab c', 3, false)      --> 'ab'
 *   truncate_after('ab c', 4, false)      --> 'ab c'
 *   truncate_after('ab c', 5, false)      --> 'ab c'
 *   truncate_after('ab      c', 5, false) --> 'ab'
 *   truncate_after('       c', 1, false)  --> 'c' // string is trimmed!
 *   truncate_after('       c', 2, false)  --> 'c'
 *   truncate_after('       c', 5, false)  --> 'c'
 *   truncate_after('abcde', 5, false)     --> 'abcde'
 *   truncate_after('abcde ', 5, false)    --> 'abcde'
 *   truncate_after('abcde ', 6, false)    --> 'abcde'
 *
 * @param array   $strings  An array of strings.
 * @param integer $limit    Limit where to cut
 * @param boolean $cutWords Do we cut words or not (optionnal).
 *
 * @return string
 */
function truncate_after($string, $limit, $doCutWord = false) {
    $string = trim($string);

    if ($limit <= 0) {
        throw new \InvalidArgumentException();
    } else if ($limit >= strlen($string)) {
        $limit = strlen($string);
    }

    if ($doCutWord) {
        $offset = $limit;
    } else {
        // Check & get next word offset
        $offsetNextWord = (strlen($string) == $limit) ? $limit : 0;
        if (preg_match('/(\W)/', substr($string, $limit), $match, PREG_OFFSET_CAPTURE)) {
            if ($match[1][1] == 0) { // Was that the last character of a word?
                $offsetNextWord = $limit + $match[1][1];
            }
        }

        if ($offsetNextWord !== 0) {
            $offset = $offsetNextWord;
        } else {
            // Get previous word offset
            $offset = 0;
            if (preg_match_all('/(\W)/', substr($string, 0, $limit), $match, PREG_OFFSET_CAPTURE)) {
                $offset = array_pop(array_pop(array_pop($match)));
            }
        }
    }

    return trim(substr($string, 0, $offset));
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
