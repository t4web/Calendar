<?php

namespace T4webCalendar\Calendar;

use T4webBase\Domain\Enum;

class Type extends Enum {

    const DAY_OF = 1;
    const HOLIDAY = 2;

    /**
     * @var array
     */
    protected static $constants = array(
        self::DAY_OF => 'Выходной',
        self::HOLIDAY => 'Праздник',
    );

}