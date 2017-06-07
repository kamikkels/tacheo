<?php

declare(strict_types=1);

namespace dt4a_challenge;

use PHPUnit\Framework\TestCase;

use dt4a_challenge\DateTimeUtilities;

/**
 * Class DateTimeUtilitiesTest
 * @package dt4a_challenge
 * @covers DateTimeUtilities
 */
class DateTimeUtilitiesTest extends TestCase
{
    /**
     * @var array - testData: an array of data to run tests over
     */
    private $testData = array(
        array(
            'start'    => '2013-12-2 12:11:10',
            'end'      => '2013-12-12 12:11:10',
            'days'     => 10,
            'expected' => array(
                'Second' => '864,000 Seconds',
                'Minute' => '14,400 Minutes',
                'Hour'   => '240 Hours',
                'Day'    => '10 Days',
                'Year'   => '0 Years',
            ),
        ),
        array(
            'start'    => '2013-12-2 12:11:10',
            'end'      => '2017-04-01 3:03:03',
            'days'     => 1215,
            'expected' => array(
                'Second' => '105,025,913 Seconds',
                'Minute' => '1,750,431 Minutes',
                'Hour'   => '29,173 Hours',
                'Day'    => '1,215 Days',
                'Year'   => '3 Years',
            ),
        ),
        array(
            'start'    => '1970-01-01 0:00:00',
            'end'      => '2038-01-08 03:14:08',
            'days'     => 24844,
            'expected' => array(
                'Second' => '2,146,533,248 Seconds',
                'Minute' => '35,775,554 Minutes',
                'Hour'   => '596,259 Hours',
                'Day'    => '24,844 Days',
                'Year'   => '68 Years',
            ),
        ),
        # Note: this test will fail on 32-bit systems
        array(
            'start'    => '1940-01-01 0:00:00',
            'end'      => '2050-01-08 03:14:08',
            'days'     => 40185,
            'expected' => array(
                'Second' => '3,471,995,648 Seconds',
                'Minute' => '57,866,594 Minutes',
                'Hour'   => '964,443 Hours',
                'Day'    => '40,185 Days',
                'Year'   => '110 Years',
            ),
        ),
    );

    /**
     * Test execution of TimeBetweenStrings with valid input
     */
    public function testCorrectTimeBetweenStrings(): void
    {
        foreach ($this->testData as $testDatum) {
            foreach ($testDatum['expected'] as $unit => $result){
                $this->assertEquals(
                    $result,
                    DateTimeUtilities::timeBetweenStrings(
                        $testDatum['start'],
                        $testDatum['end'],
                        $unit
                    ));
            }

            $this->assertEquals(
                $testDatum['days'],
                DateTimeUtilities::timeBetweenStrings(
                    $testDatum['start'],
                    $testDatum['end'],
                    'Day',
                    true
                ));
        }
    }

    /**
     * Test execution of TimeBetweenDateTimes with valid input
     */
    public function testCorrectTimeBetweenDateTimes(): void
    {
        foreach ($this->testData as $testDatum) {
            foreach ($testDatum['expected'] as $unit => $result){
                $this->assertEquals(
                    $result,
                    DateTimeUtilities::timeBetweenDateTimes(
                        new \DateTime($testDatum['start']),
                        new \DateTime($testDatum['end']),
                        $unit
                    ));
            }

            $this->assertEquals(
                $testDatum['days'],
                DateTimeUtilities::timeBetweenDateTimes(
                    new \DateTime($testDatum['start']),
                    new \DateTime($testDatum['end']),
                    'Day',
                    true
                ));
        }
    }

    /**
     * Test execution of TimeBetween with valid input
     */
    public function testCorrectTimeBetween(): void
    {
        foreach ($this->testData as $testDatum) {
            foreach ($testDatum['expected'] as $unit => $result){
                $this->assertEquals(
                    $result,
                    DateTimeUtilities::timeBetween(
                        strtotime($testDatum['start']),
                        strtotime($testDatum['end']),
                        $unit
                    ));
            }

            $this->assertEquals(
                $testDatum['days'],
                DateTimeUtilities::timeBetween(
                    strtotime($testDatum['start']),
                    strtotime($testDatum['end']),
                    'Day',
                    true
                ));
        }
    }

    /**
     * Test execution of TimeBetween with invalid inputs
     */
    public function testTimeBetweenInvalidArgumentException(): void
    {
        $testDatum = $this->testData[0];

        $this->expectException(\InvalidArgumentException::class);

        # Invalid units
        DateTimeUtilities::timeBetween(
            strtotime($testDatum['start']),
            strtotime($testDatum['end']),
            'testingInvalidArgumentException'
        );

        # Invalid start
        DateTimeUtilities::timeBetween(
            'testingInvalidArgumentException',
            strtotime($testDatum['end'])
        );

        # Invalid end
        DateTimeUtilities::timeBetween(
            strtotime($testDatum['start']),
            'testingInvalidArgumentException'
        );

        # Invalid ret_int
        DateTimeUtilities::timeBetween(
            strtotime($testDatum['start']),
            strtotime($testDatum['end']),
            'day',
            'test'
        );
    }
}