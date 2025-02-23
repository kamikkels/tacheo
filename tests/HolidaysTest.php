<?php

declare(strict_types=1);

namespace Tacheo;

use PHPUnit\Framework\TestCase;

/**
 * Class HolidaysTest
 * @covers Holidays
 */
class HolidaysTest extends TestCase
{
    /**
     * @var array - testData: an array of data to run tests over
     */
    private array $testData = [
        'single_year' => [
            'start' => '2025-01-01',
            'end' => '2025-12-31',
            'expected' => []
        ],
        'multiple_years' => [
            'start' => '2024-12-25',
            'end' => '2026-01-01',
            'expected' => []
        ],
        'holidays_between_years' => [
            'startYear' => 2024,
            'endYear' => 2026,
            'expected' => []
        ],
        'month_day' => [
            'start' => 101, // January 1st
            'end' => 1231,  // December 31st
            'year' => 2025,
            'expected' => []
        ],
        'western_easter' => [
            'year' => 2030,
            'expected' => null // Placeholder
        ],
        'eastern_easter' => [
            'year' => 2030,
            'expected' => null // Placeholder
        ]
    ];

    private Holidays $holidays;

    protected function setUp(): void
    {
        // Initialize the Holidays class with a sample locality for testing.
        $this->holidays = new Holidays(['Test Locality']);

        // Calculate and assign the mktime() values here.
        $this->testData['western_easter']['expected'] = mktime(0, 0, 0, 4, 21, 2030); // Expected date for Western Easter in 2030.
        $this->testData['eastern_easter']['expected'] = mktime(0, 0, 0, 4, 28, 2030); // Expected date for Eastern Easter in 2030.
    }

    /**
     * Test getHolidaysBetweenDates with holidays in the same year.
     */
    public function testGetHolidaysBetweenDatesSameYear()
    {
        $start = new \DateTime($this->testData['single_year']['start']);
        $end = new \DateTime($this->testData['single_year']['end']);
        $expectedHolidays = $this->testData['single_year']['expected'];

        // Call the method and assert the expected results.
        $result = $this->holidays->getHolidaysBetweenDates($start, $end);
        $this->assertEquals($expectedHolidays, $result);
    }

    /**
     * Test getHolidaysBetweenDates with holidays spanning multiple years.
     */
    public function testGetHolidaysBetweenDatesMultipleYears()
    {
        $start = new \DateTime($this->testData['multiple_years']['start']);
        $end = new \DateTime($this->testData['multiple_years']['end']);
        $expectedHolidays = $this->testData['multiple_years']['expected'];

        // Call the method and assert the expected results.
        $result = $this->holidays->getHolidaysBetweenDates($start, $end);
        $this->assertEquals($expectedHolidays, $result);
    }

    /**
     * Test getHolidaysBetweenYears for a range of years.
     */
    public function testGetHolidaysBetweenYears()
    {
        $startYear = $this->testData['holidays_between_years']['startYear'];
        $endYear = $this->testData['holidays_between_years']['endYear'];
        $expectedHolidays = $this->testData['holidays_between_years']['expected'];

        // Call the method and assert the expected results.
        $result = $this->holidays->getHolidaysBetweenYears($startYear, $endYear);
        $this->assertEquals($expectedHolidays, $result);
    }

    /**
     * Test getHolidaysBetweenMonthDay for a specific date range.
     */
    public function testGetHolidaysBetweenMonthDay()
    {
        $start = $this->testData['month_day']['start'];
        $end = $this->testData['month_day']['end'];
        $year = $this->testData['month_day']['year'];
        $expectedHolidays = $this->testData['month_day']['expected'];

        // Call the method and assert the expected results.
        $result = $this->holidays->getHolidaysBetweenMonthDay($start, $end, $year);
        $this->assertEquals($expectedHolidays, $result);
    }

    /**
     * Test getWesternEasterSunday for a specific year.
     */
    public function testGetWesternEasterSunday()
    {
        $year = $this->testData['western_easter']['year'];
        $expectedEaster = $this->testData['western_easter']['expected'];

        // Call the method and assert the expected results.
        $result = $this->holidays->getWesternEasterSunday($year);
        $this->assertEquals($expectedEaster, $result);
    }

    /**
     * Test getEasternEasterSunday for a specific year.
     */
    public function testGetEasternEasterSunday()
    {
        $year = $this->testData['eastern_easter']['year'];
        $expectedEaster = $this->testData['eastern_easter']['expected'];

        // Call the method and assert the expected results.
        $result = $this->holidays->getEasternEasterSunday($year);
        $this->assertEquals($expectedEaster, $result);
    }
}
