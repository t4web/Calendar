<?php

namespace T4webCalendar\Calendar\Criteria;

use T4webBase\Domain\Criteria\AbstractCriteria;

class Ids extends AbstractCriteria {
    
    protected $field = 'id';
    protected $table = 'calendar';
    protected $buildMethod = 'addFilterIn';
}
