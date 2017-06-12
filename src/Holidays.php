<?php
/**
 * Holidays class - handles getting holiday dates between two datetimes.
 * User: Kerry M-R
 * @Version 0.9
 */

namespace dt4a_challenge;

use Illuminate\Database\Capsule\Manager as Capsule;

class Holidays
{
    private $holidaysCapsule;
    private $locations;
    # December 31st (12/31)
    private static $endOfYear = 1231;
    # January 1st (01/01)
    private static $startOfYear = 101;

    public function __construct(array $locations)
    {
        $connectionConfig = include('config/holidaysConnection.php');

        $this->holidaysCapsule = new Capsule;
        $this->holidaysCapsule->addConnection($connectionConfig);
        $this->holidaysCapsule->setAsGlobal();
        $this->holidaysCapsule->bootEloquent();

        $this->locations = $locations;
    }

    public function getHolidaysBetweenDates(\DateTime $start, \DateTime $end, bool $inc_partday = false): array
    {
        # Check what time period we're looking at, we'll need to select things slightly differently
        # depending on time spans due to re-occurring holidays
        $interval = $start->diff($end);
        if($interval->y > 0 && !($interval->d == 0 && $interval->m == 0))
        {
            # Get the holidays from the start date till the end of the first year (year specific + re-occurring)
            $holidays = $this->getHolidaysBetweenMonthDay(
                (int)$start->format('md'),
                $this->endOfYear,
                (int)$start->format('Y'),
                $inc_partday
            );

            # See if there are intermediate years and get holidays for each of them (year specific + re-occurring)
            if(((int)$start->format('Y') - (int)$end->format('Y')) > 1) {
                $genericHolidays = $this->getHolidaysBetweenMonthDay(
                    self::$startOfYear,
                    self::$endOfYear,
                    null,
                    $inc_partday);
                # Note: this isn't the most efficient loop, but the difference only really comes out if you're
                # looking at over 1,000 years of holidays, and there's other places this could be improved first
                foreach (range(((int)$start->format('Y') + 1), ((int)$end->format('Y') - 1)) as $year) {
                    $holidaysForYear = $this->getHolidaysBetweenMonthDay(
                        self::$startOfYear,
                        self::$endOfYear,
                        $year,
                        $inc_partday);
                    $holidays = array_merge($holidays, $holidaysForYear, $this->addYearToArrayKeys($genericHolidays, $year));
                }
            }

            # Get the holidays form the start of the year till the end date (year specific + re-occurring)
            $holidays = array_merge($holidays,
                $this->getHolidaysBetweenMonthDay(
                    $this->startOfYear,
                    (int)$end->format('md'),
                    (int)$end->format('Y'),
                    $inc_partday
                )
            );
            # Sort everything and return it
            sort($holidays);
            return $holidays;

        } elseif ((int)$start->format('md') > (int)$end->format('md'))
        {

            # Get the holidays from the start date till the end of the first year (year specific + re-occurring)
            $holidays = $this->getHolidaysBetweenMonthDay(
                (int)$start->format('md'),
                $this->endOfYear,
                (int)$start->format('Y'),
                $inc_partday
            );

            # Get the holidays form the start of the year till the end date (year specific + re-occurring)
            $holidays = array_merge($holidays,
                $this->getHolidaysBetweenMonthDay(
                    $this->startOfYear,
                    (int)$end->format('md'),
                    (int)$end->format('Y'),
                    $inc_partday
                )
            );
            # Sort everything and return it
            sort($holidays);
            return $holidays;
        }

        return $this->getHolidaysBetweenMonthDay(
            (int)$start->format('md'),
            (int)$end->format('md'),
            (int)$end->format('Y'),
            $inc_partday
        );
    }

    public function getHolidaysBetweenMonthDay(int $start, int $end, int $year = null, bool $inc_partday = false): array
    {
        $holidays = Capsule::table('Holidays')
            ->join('Holiday_Locations', 'Holidays.id', '=', 'Holiday_Locations.Holiday_id')
            ->join('Locations', 'Locations.id', '=', 'Holiday_Locations.Location_id')
            ->whereIn('Locations.Name', $this->locations)
            ->whereBetween('Holidays.Month_Day', [$start, $end])
            ->whereIn('Holidays.Year', [$year, null])
            ->get();

        foreach($holidays as $holiday)

        return $holidaysAssoc;
    }

    /**
     * getWesternEasterSunday
     *
     * Returns a timestamp for midnight on easter sunday for any given year
     * Based on the Western Gregorian calendar easter.
     *
     * @param $year
     * @return false|int
     */
    public function getWesternEasterSunday($year)
    {
        $a = $year % 19;
        $b = $year / 100;
        $c = $year % 100;
        $d = $b / 4;
        $e = $b % 4;
        $f = ($b + 8) / 25;
        $g = ($b - $f + 1) / 3;
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = $c / 4;
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = ($a + 11 * $h + 22 * $l) / 451;
        $month = (($h + $l - 7 * $m + 114) / 31) - 1;
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        $easterSunday = mktime(0, 0, 0, $month, $day, $year);

        return $easterSunday;
    }

    /**
     * getEasternEasterSunday
     *
     * Returns a timestamp for midnight on easter sunday for any given year
     * Based on the Eastern Julian calendar easter.
     *
     * @param $year
     * @return false|int
     */
    public function getEasternEasterSunday($year)
    {
        $a = $year % 4;
        $b = $year % 7;
        $c = $year % 19;
        $d = (19 * $c + 15) % 30;
        $e = (2 * $a + 4 * $b - $d + 34) % 7;
        $month = floor(($d + $e + 114) / 31);
        $day = (($d + $e + 114) % 31) + 14;

        $easterSunday = mktime(0, 0, 0, $month, $day, $year);

        return $easterSunday;
    }

    public function addYearToArrayKeys(array $array, int $year): array
    {
        # First get all the keys to remap
        $keys = array_keys($array);

        # Perform internal iteration with prefix passed into walk function for dynamic replace of key
        array_walk($keys, 'addYearToKey', "$year");

        # Combine the rewritten keys and overwrite the originals
        return array_combine($keys, $array);
    }

    public function addYearToKey(&$value, $omit, $prefix){
        $value = "$prefix$value";
    }

    /**
     * Generic getter, will get the value of any property
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property))
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
        if (property_exists($this, $property))
        {
            $this->$property = $value;
        }

        return $this;
    }
}