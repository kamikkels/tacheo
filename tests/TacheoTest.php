<?php

declare(strict_types=1);

namespace dt4a_challenge;

use PHPUnit\Framework\TestCase;

use dt4a_challenge\Tacheo;

/**
 * Class TacheoTest
 * @package dt4a_challenge
 * @covers Tacheo
 */
class TacheoTest extends TestCase
{
    /**
     * @var array - testData: an array of data to run tests over
     */
    private $testData = array(
        array(
            'start'    => '2013-12-2 12:11:10',
            'end'      => '2013-12-9 12:11:10',
            'days'     => 10,
            'expected' => array(
                'Second' => '604,800 Seconds',
                'Minute' => '10,080 Minutes',
                'Hour'   => '168 Hours',
                'Week'   => '1 Week',
                'Day'    => '7 Days',
                'Year'   => '0 Years',
                'Complete weeks' => '0 Weeks',
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
                'Week'   => '173 Weeks',
                'Year'   => '3 Years',
                'Complete weeks' => '173 Weeks',
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
                'Week'   => '3,549 Weeks',
                'Year'   => '68 Years',
                'Complete weeks' => '3,549 Weeks',
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
                'Week'   => '5,740 Weeks',
                'Year'   => '110 Years',
                'Complete weeks' => '5,740 Weeks',
            ),
        ),
    );

    private $dateFormat = 'Y-m-d H:i';

    /**
     * Test execution of timeBetween with valid input
     */
    public function testCorrectTimeBetween(): void
    {
        # New line to make sure everything is nicely spaced
        print PHP_EOL;

        foreach ($this->testData as $testDatum) {
            foreach ($testDatum['expected'] as $unit => $expectedResult){
                $start = date_create($testDatum['start']);
                $end   = date_create($testDatum['end']);

                $testTacheo = new Tacheo($start, $end);

                print "$unit - $expectedResult" . PHP_EOL;

                $this->assertEquals(
                    $expectedResult,
                    $testTacheo->timeBetween( $unit )
                );
            }
        }
    }

    /**
     * Test execution of TimeBetween with invalid inputs
     */
    public function testExceptions(): void
    {
        $this->expectException(\TypeError::class);

        # Invalid start and end
        $testTacheo = new Tacheo('test', 'test');

        $this->expectException(\InvalidArgumentException::class);

        # Working construct to test invalid args
        $testTacheo = new Tacheo(new \DateTime(), new \DateTime());

        # Invalid units
        $testTacheo->timeBetween('testingInvalidArgumentException');

        # Invalid returnInt
        $testTacheo->timeBetween('day', 'test');

    }
}