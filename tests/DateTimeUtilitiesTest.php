<?php

declare(strict_types=1);

namespace dt4a_challenge;

use PHPUnit\Framework\TestCase;

use dt4a_challenge\DateTimeUtilities;

class DateTimeUtilitiesTest extends TestCase
{
    private $testData = array(
        array(
            'dt1'      => '2013-12-2 12:11:10',
            'dt2'      => '2013-12-12 12:11:10',
            'expected' => array(
                'Second' => '864000 Seconds',
                'Minute' => '14400 Minutes',
                'Hour'   => '240 Hours',
                'Day'    => '10 Days',
                'Year'   => '0 Years',
            ),
        ),
    );

    private static $dtFormat = 'Y-m-d H:i:s';

}