<?php
/**
 * Holidays class - handles getting holiday dates between two datetimes.
 * User: Kerry M-R
 * @Version 0.9.1
 */

namespace Tacheo;

define('INC_ROOT', dirname(__DIR__, 1));
require INC_ROOT . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

class Holidays
{
    private Capsule $holidaysCapsule;
    private array $locality;
    # January 1st (01/01)
    private const START_OF_YEAR = 101;
    # December 31st (12/31)
    private const END_OF_YEAR = 1231;

    public function __construct(array $locality)
    {
        $connectionConfig = include('config/holidaysConnection.php');

        $this->holidaysCapsule = new Capsule;
        $this->holidaysCapsule->addConnection($connectionConfig);
        $this->holidaysCapsule->setAsGlobal();
        $this->holidaysCapsule->bootEloquent();

        $this->locality = $locality;
    }

    /**
     * getHolidaysBetweenDates
     *
     * Get all holiday days between two DateTime objects
     *
     * @param \DateTime $start       - The start DateTime
     * @param \DateTime $end         - The end DateTime
     * @param bool|null $inc_partday - whether to include part-day holidays (optional, defaults to false)
     * @return array - An array of all holidays between the DateTimes, indexed on year-month-day
     */
    public function getHolidaysBetweenDates(\DateTime $start, \DateTime $end, bool $inc_partday = false): array
    {
        # Check what time period we're looking at, we'll need to select things slightly differently
        # depending on time spans due to re-occurring holidays
        $interval = $start->diff($end);
        if($interval->y > 0 && !($interval->d == 0 && $interval->m == 0)) {
            $holidays = array_merge(
                $this->getHolidaysBetweenMonthDay( # Get the holidays from the start date till the end of the first year
                    (int)$start->format('md'), self::$endOfYear, (int)$start->format('Y'), $inc_partday),
                $this->getHolidaysBetweenYears( # See if there are intermediate years and get holidays for each of them
                    (int)$start->format('Y'), (int)$end->format('Y'), $inc_partday),
                $this->getHolidaysBetweenMonthDay( # Get the holidays from the start of the year till the end date (year specific + re-occurring)
                    self::$startOfYear, (int)$end->format('md'), (int)$end->format('Y'), $inc_partday)
            );

            # Sort everything and return it
            sort($holidays);
            return $holidays;

        } elseif ((int)$start->format('md') > (int)$end->format('md')) {
            $holidays = array_merge(
                $this->getHolidaysBetweenMonthDay( # Get the holidays from the start date till the end of the first year
                    (int)$start->format('md'), $this->endOfYear, (int)$start->format('Y'), $inc_partday),
                $this->getHolidaysBetweenMonthDay( # Get the holidays form the start of the year till the end date
                    $this->startOfYear, (int)$end->format('md'), (int)$end->format('Y'), $inc_partday)
            );
        } else {
            # Get the holidays from the start date till the end date (year specific + re-occurring)
            $holidays =  $this->getHolidaysBetweenMonthDay((int)$start->format('md'), (int)$end->format('md'), (int)$end->format('Y'), $inc_partday);
        }
        # Sort everything and return it
        sort($holidays);
        return $holidays;
    }

    /**
     * getHolidaysBetweenYears
     *
     * Get all holidays that exist between two years (exclusive)
     *
     * @param int $start             - The starting year that holidays must occur after
     * @param int $end               - The ending year that holidays must occur before
     * @param bool|null $inc_partday - whether to include part-day holidays (optional, defaults to false)
     * @return array - An array of all holidays between the DateTimes, indexed on year-month-day
     */
    public function getHolidaysBetweenYears(int $start, int $end, bool $inc_partday = false): array
    {
        if(((int)$start->format('Y') - (int)$end->format('Y')) > 1) {
            return array_merge(
                ...array_map(
                    fn($year) => $this.getHolidaysBetweenMonthDay(self::START_OF_YEAR, self::END_OF_YEAR, $year, $incPartDay),
                    range($start + 1, $end - 1)
                )
            );
        }
    }

    /**
     * getHolidaysBetweenMonthDay
     *
     * Get all holiday days that occure between two days
     *
     * @param int $start             - The start month_day integer
     * @param int $end               - The end month_day integer
     * @param int|null $year         - The year to seach within (optional, if null will only return recurring holidays)
     * @param bool|null $inc_partday - whether to include part-day holidays (optional, defaults to false)
     * @return array - An array of all holidays between the DateTimes, indexed on year-month-day
     */
    public function getHolidaysBetweenMonthDay(int $start, int $end, int $year = null, bool $inc_partday = false): array
    {
        $holidays = Capsule::table('holidays')
            ->join('holiday_localities', 'holidays.id', '=', 'holiday_localities.holiday_id')
            ->join('locality', 'locality.id', '=', 'holiday_localities.locality_id')
            ->whereIn('locality.locality_name', $this->locality)
            ->whereBetween('holidays.month_day', [$start, $end])
            ->where(fn($query) => $query->where('holidays.year', $year)->orWhereNull('holidays.year'))
            ->get();

        $holidaysAssoc = [];

        foreach($holidays as $holiday)
        {
            $index = ($holiday->year ? $holiday->year : $year ) . str_pad($holiday->month_Day, 4, "0",  STR_PAD_LEFT);

            if(!is_null($year)) {
                if($holiday->holiday_name === 'Easter') {
                    $index = $this->getWesternEasterSunday($year) + 1;
                    $holidaysAssoc[$index - 3] = $holiday;

                } elseif ($holiday->holiday_name === 'Eastern Easter') {
                    $index = $this->getEasternEasterSunday($year) + 1;
                    $holidaysAssoc[$index - 3] = $holiday;
                }
            }

            $holidaysAssoc[$index] = $holiday;
        }

        return $holidaysAssoc;
    }

    /**
     * getWesternEasterSunday
     *
     * Returns a timestamp for midnight on easter sunday for any given year
     * Based on the Western Gregorian calendar easter.
     *
     * @param $year - The year to find Western Easter Sunday in
     * @return int - the unix timestamp for midnight on Easter Sunday
     */
    public function getWesternEasterSunday($year): int
    {
        $a = $year % 19;
        $b = $year / 100;
        $c = $year % 100;
        $d = $b / 4;
        $e = $b % 4;
        $g = ((8 * $b) + 13) / 25;
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = $c / 4;
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = ($a + 11 * $h + 22 * $l) / 451;
        $month = (($h + $l - 7 * $m + 114) / 31);
        $day = ($h + $l - (7 * $m) + (33 * $n) + 19) % 33;

        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * getEasternEasterSunday
     *
     * Returns a timestamp for midnight on easter sunday for any given year
     * Based on the Eastern Julian calendar easter.
     *
     * @param $year - The year to find Eastern / Julian Calendar Easter Sunday in
     * @return int - The unix timestamp for midnight on Easter Sunday
     */
    public function getEasternEasterSunday($year): int
    {
        $a = $year % 4;
        $b = $year % 7;
        $c = $year % 19;
        $d = (19 * $c + 15) % 30;
        $e = (2 * $a + 4 * $b - $d + 34) % 7;
        $month = floor(($d + $e + 114) / 31);
        $day = (($d + $e + 114) % 31) + 14;

        return mktime(0, 0, 0, $month, $day, $year);
    }
}