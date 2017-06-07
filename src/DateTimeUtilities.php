<?php

declare(strict_types=1);

namespace dt4a_challenge;

use \DateTime;
use phpDocumentor\Reflection\Types\String_;

/**
 * Class DateTimeUtils
 *
 * DateTime utilities class to provide useful time between functions
 *
 * @package dt4a_challenge
 */

class DateTimeUtilities
{
    /**
     * timeBetween
     *
     * DateTime comparison function, returns the quantity of whole units between the two datetimes
     *
     * @param \DateTime $start - DateTime object for comparison
     * @param \DateTime $end   - Second DateTime objectfor comparison
     * @param String $unit : 'day'     - Units to define comparison output
     * @param bool $ret_int            - Flag to return the integer difference only
     * @return String                  - The quantitiy of whole units between the two DateTime objects
     */

    public static function timeBetweenDateTimes(\DateTime $start, \DateTime $end, String $unit = 'day', bool $ret_int = false)
    {
        return self::timeBetween($start->getTimestamp(), $end->getTimestamp(), $unit, $ret_int);
    }

    /**
     * timeBetweenStrings
     *
     * DateTime comparison function, returns the quantity of whole units between the two strings passed in
     *
     * @param String $start - Start datetime for comparison
     * @param String $end   - End datetime for comparison
     * @param String $unit  - Units to define comparison output
     * @param bool $ret_int - Flag to return the integer difference only
     * @return String       - The quantitiy of whole units between the two datetimes objects
     */
    public static function timeBetweenStrings(String $start, String $end, String $unit = 'day', bool $ret_int = false)
    {
        return self::timeBetween(strtotime($start), strtotime($end), $unit, $ret_int);
    }

    /**
     *
     *
     * @param int $start    - Start timestamp for comparison
     * @param int $end      - End timestamp for comparison
     * @param String $unit  - Units to define comparison output
     * @param bool $ret_int - Flag to return the integer difference only
     * @return String       - The quantitiy of whole units between the two timestamps
     */
    public static function timeBetween(int $start, int $end, String $unit = 'day', bool $ret_int = false)
    {
        # Get the config'd defaults
        $defaultConfig = self::getDefaults();

        # Make the $units arg lowercase and strip off any trailing s
        $unit = rtrim(strtolower($unit), 's');
        # Check the key is in the config, should allow easy expansion if anyone feels so inclined
        if(!array_key_exists($unit, $defaultConfig['length'])){
            $unitTypes = join('(s), ', array_keys($defaultConfig['length']));
            throw new \InvalidArgumentException('Invalid unit value passed in, units must be in ' . $unitTypes);
        }

        $secondsDifference = $end - $start;
        $interval = floor($secondsDifference / $defaultConfig['length'][$unit]);

        if($ret_int){
            return $interval;
        }
        return self::humanise((int)$interval, $unit);
    }

    /**
     * pluralise
     *
     * Return nicely formated quantity with postfixed units
     *
     * @param int $quantity - the quantity of units
     * @param String $unit  - units to postfix the quantity
     * @return string       - quantity postfixed by pluralised units if required e.g. 45 Days
     */
    private static function humanise(int $quantity, String $unit): String
    {
        return number_format($quantity) . ' ' . ucfirst($unit) . (($quantity != 1 && $quantity != -1) ? 's' : '');
    }

    /**
     * getDefaults
     *
     * Returns the default values configuration from config/defaults.php
     *
     * @return mixed
     */
    private static function getDefaults()
    {
        return include('config/defaults.php');
    }

}