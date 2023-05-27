<?php

namespace Appkit\Toolkit;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

/**
 * Date
 *
 * @package   Toolkit
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class Date
{
    public static function addDays(DateTime $date, int $days): DateTime
    {
        $date->modify("+{$days} days");
        return $date;
    }

    public static function age(string $date, string $format = 'Y-m-d'): int
    {
        $today = new DateTime();
        $diff = $today->diff(DateTime::createFromFormat($format, $date));
        return $diff->y;
    }

    public static function diffInDays(string $date1, string $date2, string $format = 'Y-m-d'): int
    {
        $diff = DateTime::createFromFormat($format, $date1)->diff(DateTime::createFromFormat($format, $date2));
        return (int)$diff->format('%R%a');
    }

    public static function format(string $date, string $fromFormat = 'd-m-Y', string $toFormat = 'Y-m-d'): string
    {
        return DateTime::createFromFormat($fromFormat, $date)->format($toFormat);
    }

    public static function isToday(string $date, string $format = 'Y-m-d'): bool
    {
        return date('Y-m-d') === date($format, strtotime($date));
    }

    public static function isValid(string $date): bool
    {
        return V::date($date);
    }

    public static function before(string $date1, string $date2, string $format = 'Y-m-d'): bool
    {
        return DateTime::createFromFormat($format, $date1) < DateTime::createFromFormat($format, $date2);
    }


    /**
     * @throws Exception
     */
    public static function range(string $start, string $end): DatePeriod
    {
        return new DatePeriod(new DateTime($start), new DateInterval('P1D'), (new DateTime($end))->modify('+1 day'));
    }
}
