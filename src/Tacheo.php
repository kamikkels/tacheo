<?php
/**
 * Created by IntelliJ IDEA.
 * User: kerrymr
 * Date: 8/06/2017
 * Time: 4:41 PM
 */

namespace dt4a_challenge;

use dt4a_challenge\Holidays;


class Tacheo
{
    private $start;
    private $end;
    private $weekStartDay;
    private $timeLengths;
    private $workdays;
    private $daysInWeek;

    /**
     * Tacheo constructor.
     *
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(\DateTime $start, \DateTime $end)
    {
        $defaults = include('config/defaults.php');

        $this->start = $start;
        $this->end = $end;

        $this->weekStartDay = $defaults['week_start_day'];
        $this->timeLengths  = $defaults['lengths'];
        $this->workdays     = $defaults['workdays'];
        $this->daysInWeek   = $defaults['days_in_week'];

        $this->timeLengths['week'] = $this->timeLengths['day'] * $this->daysInWeek;
    }


    /**
     * timeBetween
     *
     * Timestamp comparison function, returns the quantity of whole units between start and end.
     *
     * @param int $start    - Start timestamp for comparison
     * @param int $end      - End timestamp for comparison
     * @param String $unit  - Units to define comparison output
     * @param bool $retInt  - Flag to return the integer difference only
     * @return String|int   - The quantity of whole units between the two timestamps
     */
    public function timeBetween(String $unit = 'day', bool $retInt = false)
    {
        # Make the $units arg lowercase and strip off any trailing s
        $unit = rtrim(strtolower($unit), 's');
        # Alias for completeWeeksBetween for consistency
        if($unit === 'complete week'){
            return $this->completeWeeksBetween('default', $retInt);
        }

        # Check the key is in the config, should allow easy expansion if anyone feels so inclined
        if(!array_key_exists($unit, $this->timeLengths)){
            $unitTypes = join('(s), ', array_keys($this->timeLengths)) . '(s), Complete week(s)';
            throw new \InvalidArgumentException("Invalid unit value [$unit] passed in," . PHP_EOL . "units must be in $unitTypes");
        }

        $secondsDifference = $this->end->getTimestamp() - $this->start->getTimestamp();
        $interval = floor($secondsDifference / $this->timeLengths[$unit]);

        if($retInt){
            return $interval;
        }
        return $this->humanise($interval, $unit);
    }


    public function workingDaysBetween(array $locations, bool $incPartDays = false, bool $retInt = false)
    {
        $holidayConnection = new Holidays($locations);
        $holidays = $holidayConnection->getHolidaysBetween($this->start, $this->end, $incPartDays);


    }
    
    /**
     * completeWeeksBetween
     *
     * Determine the volume of complete weeks starting and ending on a particular day. e.g. Monday to Sunday
     *
     * @param String $startDay : '1' - The numeric representation for the first day of the week
     * @return String|int            - The quantity of whole weeks from the start timestamp till the end timestamp
     */
    public function completeWeeksBetween(String $startDay = 'default', bool $retInt = false)
    {
        # Set the default startDay if needed, throw some exceptions if needed
        if(strtolower($startDay) === 'default'){
            $startDay = $this->weekStartDay;
        } elseif (!is_int($startDay) || $startDay > $this->daysInWeek || $startDay < 1 ){
            throw new \InvalidArgumentException("Invalid startDay value passed in, startDay must be either default or 1 to $this->daysInWeek");
        }
        # Calculate the endDay
        $endDay = ($startDay - 1) > 0 ? $startDay - 1 : $this->daysInWeek;

        # Calculate how many days there are between start and end, and how many days from the start and end of the week they are
        $secondsDifference = $this->end->getTimestamp() - $this->start->getTimestamp();
        $rawWeekDays = floor($secondsDifference / $this->timeLengths['day']);

        $partWeekdaysAtStart = $this->weekdaysBetween((int)$this->start->format('%d'), $startDay);
        $partWeekdaysAtEnd   = $this->weekdaysBetween($endDay, (int)$this->end->format('%d'));

        # Work out how many whole weeks there are
        $weekCount = ($rawWeekDays - ($partWeekdaysAtStart + $partWeekdaysAtEnd)) / $this->daysInWeek;

        # Return the raw integer value if needed, or nice string if not
        if($retInt){
            return $weekCount;
        }
        return self::humanise((int)$weekCount, 'week');
    }

    /**
     * weekdaysFrom
     *
     * Determine the number of weekdays between two numbers representing weekdays
     *
     * @param int $start - The start day's numeric representation
     * @param int $end   - The end day's numeric representation
     * @return int       - The quantity of days from the start day to the next ocuring end day
     */
    public function weekdaysBetween(int $start, int $end): int
    {
        # If the end day is earlier in the week sequence advance it to the next week
        if($end < $start) {
            $end = $end + $this->daysInWeek;
        }

        $interval = $end - $start;

        return $interval;
    }

    /**
     * humanise
     *
     * Return nicely formated quantity with postfixed units
     *
     * @param int $quantity - the quantity of units
     * @param String $unit  - units to postfix the quantity
     * @return string       - quantity postfixed by pluralised units if required e.g. 45 Days
     */
    private function humanise(int $quantity, String $unit): String
    {
        return number_format($quantity) . ' ' . ucfirst($unit) . (($quantity != 1 && $quantity != -1) ? 's' : '');
    }

    /**
     * Generic getter, will get the value of any property
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if(property_exists($this, $property))
        {
            return $this->$property;
        }

        throw new \InvalidArgumentException("Unable to get $property property of type $property does not exist");
    }

    /**
     * Generic setter, will set the value of any property
     *
     * @param $property
     * @param $value
     * @return $this
     */
    public function __set($property, $value)
    {
        if(property_exists($this, $property))
        {
            $this->$property = $value;
        }

        return $this;
    }

}