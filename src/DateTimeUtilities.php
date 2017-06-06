<?php

declare(strict_types=1);

namespace dt4a_challenge;

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
     * @param \DateTime $startDateTime - DateTime object for comparison
     * @param \DateTime $endDateTime   - Second DateTime objectfor comparison
     * @param String $unit : 'day' - Units to define comparison output
     * @return String - The quantitiy of whole units between the two DateTime objects
     */
    function timeBetween(\DateTime $startDateTime, \DateTime $endDateTime, String $unit = 'day') : String {
        # Get the config'd defaults
        $defaultConfig = $this->getDefaults();

        # Make the $units arg lowercase and strip off any trailing s
        $unit = rtrim(strtolower($unit), 's');
        # Check the key is in the config, should allow easy expansion if anyone feels so inclined
        if(!array_key_exists($unit, $defaultConfig['length'])){
            $unitTypes = join('(s), ', array_keys($defaultConfig['length']));
            throw new \InvalidArgumentException('Invalid unit value passed in, units must be in ' . $unitTypes);
        }

        $interval = (int)$startDateTime->diff($endDateTime)->format($defaultConfig['length'][$unit]);

        return $this->pluralise($interval, $unit);
    }

    /**
     * timeBetweenStrings
     *
     * DateTime comparison function, returns the quantity of whole units between the two strings passed in
     *
     * @param String $start
     * @param String $end
     * @param String $unit
     * @return String
     */
    function timeBetweenStrings(String $start, String $end, String $unit = 'day') : String {
        $defaultConfig = $this->getDefaults();

        $startDateTime = strtotime($start);
        $endDateTime = strtotime($end);

        return $this->timeBetween($startDateTime, $endDateTime, $unit);
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
    private function pluralise(Integer $quantity, String $unit){
        return $quantity . ucfirst($unit) . (($quantity != 1 && $quantity != -1) ? 's' : '');
    }

    /**
     * getDefaults
     *
     * Returns the default values configuration from config/defaults.php
     *
     * @return mixed
     */
    private function getDefaults(){
        return include('config/defaults.php');
    }

}