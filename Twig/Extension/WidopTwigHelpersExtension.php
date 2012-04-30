<?php

/*
 * This file is part of the Widop package.
 *
 * (c) Widop <contact@widop.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Widop\TwigExtensionsBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface,
    Symfony\Component\Translation\TranslatorInterface;

/**
 * This class declares custom twig filters and functions.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Geoffrey Brier <geoffrey@widop.com>
 */
class WidopTwigHelpersExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface A translator.
     */
    private $translator;

    /**
     * @var string The strftime format (used by the datei18n filter|function).
     */
    private $format;

    /**
     * The constructor needs a translator (and the locale for the moment).
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator The translator.
     * @param string                                             $locale     The variable %locale%.
     */
    public function __construct(TranslatorInterface $translator, $locale)
    {
        $this->setTranslator($translator);
        $this->setFormat("%d. %B, %G");

        /**
         * @todop Use the user locale insted of the default locale.
         */
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
     * Gets the datei18n format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets the datei18n format.
     *
     * @param string $format The date format.
     *
     * @return Widop\TwigExtensionsBundle\Twig\Extension\WidopTwigHelpersExtension
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'date_interval' => new \Twig_Filter_Method($this, 'date_interval', array('is_safe' => array('html'))),
            'truncate_at'   => new \Twig_Filter_Method($this, 'truncate_at', array('is_safe' => array('html'))),
            'datei18n'      => new \Twig_Filter_Method($this, 'datei18n', array('is_safe' => array('html')))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'date_interval' => new \Twig_Function_Method($this, 'date_interval', array('is_safe' => array('html'))),
            'truncate_at' => new \Twig_Function_Method($this, 'truncate_at', array('is_safe' => array('html'))),
            'datei18n'      => new \Twig_Filter_Method($this, 'datei18n', array('is_safe' => array('html')))
        );
    }

    /**
     * Return a nice date output, very similar to a countdown.
     * This method works both as a filter and a method, and handles I18N and P10N.
     *
     * Examples:
     *  - {{ date('-2days') | date_interval }}                  --> 2 days ago
     *  - {{ date_interval('now') }} <==> {{ date_interval() }} --> A few moments ago
     *  - {{ date_interval(date('-1years')) }}                  --> A year ago
     *
     * NB: If the $date parameter is null, the method will try to convert it into a datetime.
     *     If no parameter is passed, the method will take 'now' as a default date.
     *
     * @param string|\DateTime $date Either a Datetime or a string (which can be turned into a Datetime).
     *
     * @return string
     */
    public function date_interval($date = null)
    {
        $now = new \DateTime();
        $interval = $now->diff(($date instanceof \DateTime) ? $date : new \DateTime($date));

        if ($interval->y !== 0) {
            $intervalNumber = $interval->y;
            $moment = 'date_interval.year';
        } else if ($interval->m !== 0) {
            $intervalNumber = $interval->m;
            $moment = 'date_interval.month';
        } else if ($interval->d !== 0) {
            $intervalNumber = $interval->d;
            $moment = 'date_interval.day';
        } else if ($interval->h !== 0) {
            $intervalNumber = $interval->h;
            $moment = 'date_interval.hour';
        } else if ($interval->i !== 0) {
            $intervalNumber = $interval->i;
            $moment = 'date_interval.minute';
        } else {
            return $this->translator->trans('date_interval.second');
        }

        $moment = $this->translator->transChoice($moment, $intervalNumber);

        return $this->translator->trans('%nb% %moment%', array(
            '%nb%'      => $intervalNumber,
            '%moment%'  => $moment
        ));
    }

    /**
     * Specially truncate a string given a limit.
     *
     * If this limit is higher than the length of the string, then the trimmed string
     * is returned. If this limit is inferior or equal to 0, then an exception is thrown.
     *
     * This method works both as a filter and a method, and handles I18N and P10N.
     *
     * NB: The string is first trimmed.
     *
     * Given the $cutWord parameter, the behaviour of the method may change.
     *
     * Examples:
     *  - {{ 'ab cd' | truncate_at(3) }}     --> 'ab'
     *  - {{ truncate_at('ab cd', 3) }}      --> 'ab'
     *
     *  - {{ truncate_at('ab c', 1) }}       --> ''
     *  - {{ truncate_at('ab c', 1, true) }} --> 'a'
     *
     *  - {{ truncate_at('ab c', 2) }}       --> 'ab'
     *  - {{ truncate_at('ab c', 3) }}       --> 'ab'
     *  - {{ truncate_at('ab c', 4) }}       --> 'ab c'
     *  - {{ truncate_at('ab c', 5) }}       --> 'ab c'
     *
     *  - {{ truncate_at('ab      c', 5) }}  --> 'ab'
     *  - {{ truncate_at('       c', 1) }}   --> 'c' // string is trimmed!
     *  - {{ truncate_at('       c', 2) }}   --> 'c' // string is trimmed!
     *  - {{ truncate_at('       c', 5) }}   --> 'c' // string is trimmed!
     *
     *  - {{ truncate_at('abcde', 5)  }}     --> 'abcde'
     *  - {{ truncate_at('abcde ', 5) }}     --> 'abcde'
     *  - {{ truncate_at('abcde ', 6) }}     --> 'abcde'
     *
     * @param array   $string  The string to truncate.
     * @param integer $limit   The limit where to cut.
     * @param boolean $cutWord TRUE if a word can be cutted else FALSE.
     *
     * @return string
     */
    public function truncate_at($string, $limit, $cutWord = false)
    {
        $string = trim($string);

        if ($limit <= 0 || !is_int($limit) || !is_bool($cutWord)) {
            throw new \InvalidArgumentException();
        }

        if ($limit >= strlen($string)) {
            return $string;
        }

        $offset = $limit;

        if (!$cutWord) {
            $charAfterOffset = $string[$offset];

            if (!preg_match('/\W/', $charAfterOffset)) {
                // We are trying to cut a word in half, we need to find the end of the previous word
                $offset = 0;
                if (preg_match_all('/(\W)/', substr($string, 0, $limit), $match, PREG_OFFSET_CAPTURE)) {
                    $match = array_pop($match);
                    $match = array_pop($match);
                    $offset = array_pop($match);
                }
            }
        }

        return trim(substr($string, 0, $offset));
    }

    /**
     * Format a date (with I18N).
     *
     * @param \DateTime|null $datetime The datetime to format.
     * @param string|null    $format   The format to used.
     *
     * @return string
     */
    public function datei18n(\DateTime $datetime = null, $format = null)
    {
        if ($datetime === null) {
            $datetime = new \DateTime();
        }

        if ($format === null) {
            $format = $this->format;
        }

        return strftime($format, $datetime->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'widop_twig_helpers';
    }
}
