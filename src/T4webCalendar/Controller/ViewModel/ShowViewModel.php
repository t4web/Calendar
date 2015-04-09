<?php

namespace T4webCalendar\Controller\ViewModel;

use Zend\View\Model\ViewModel;
use T4webCalendar\Calendar\CalendarCollection;


class ShowViewModel extends ViewModel
{
    /**
     * @var CalendarCollection
     */
    private $calendar;

    /**
     * @return CalendarCollection
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param CalendarCollection $calendar
     */
    public function setCalendar(CalendarCollection $calendar)
    {
        $this->calendar = $calendar;
    }
}