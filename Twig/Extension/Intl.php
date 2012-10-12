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

/**
 * Widop intl extension for twig.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Intl extends \Twig_Extension
{
    /**
     * Intl extension constructor.
     */
    public function __construct()
    {
        if (!class_exists('IntlDateFormatter')) {
            throw new RuntimeException('The intl extension is needed to use intl-based filters.');
        }
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'wlocalizeddate' => new \Twig_Filter_Method($this, 'localized_date_filter', array('is_safe' => array('html'))),
        );
    }

    /**
     * Localize a date.
     *
     * @param string|\DateTime $date       The date.
     * @param string           $dateFormat The date format.
     * @param string           $timeFormat The time format.
     * @param string           $locale     The locale.
     *
     * @return string The localized date.
     */
    function localized_date_filter($date, $dateFormat = 'medium', $timeFormat = 'medium', $locale = null)
    {
        $formatValues = array(
            'none'   => \IntlDateFormatter::NONE,
            'short'  => \IntlDateFormatter::SHORT,
            'medium' => \IntlDateFormatter::MEDIUM,
            'long'   => \IntlDateFormatter::LONG,
            'full'   => \IntlDateFormatter::FULL,
        );

        $formatter = \IntlDateFormatter::create(
            $locale !== null ? $locale : \Locale::getDefault(),
            $formatValues[$dateFormat],
            $formatValues[$timeFormat],
            date_default_timezone_get()
        );

        if (!$date instanceof \DateTime) {
            if (ctype_digit((string) $date)) {
                $date = new \DateTime('@'.$date);
                $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            } else {
                $date = new \DateTime($date);
            }
        }

        return $formatter->format($date->getTimestamp());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'intl';
    }
}
