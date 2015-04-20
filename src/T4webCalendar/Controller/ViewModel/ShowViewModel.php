<?php

namespace T4webCalendar\Controller\ViewModel;

use T4webCalendar\Calendar;
use Zend\Form\Element\DateTime;
use Zend\View\Model\ViewModel;

class ShowViewModel extends ViewModel
{
    /**
     * @var \DateTime
     */
    private $current;

    /**
     * @var Calendar\CalendarCollection
     */
    private $calendar;

    /**
     * @var array
     */
    private $calendarByDate = null;

    /**
     * @return \DateTime
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param \DateTime $current
     */
    public function setCurrent($current)
    {
        $this->current = $current;
    }

    /**
     * @return Calendar\CalendarCollection
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param Calendar\CalendarCollection $calendar
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;
    }

    public function getCalendarByDate($date) {

        if($this->calendarByDate === null) {

            foreach($this->getCalendar() as $calendar) {
                $this->calendarByDate[$calendar->getDate()] = $calendar;
            }
        }

        if(isset($this->calendarByDate[$date])) {
            return $this->calendarByDate[$date];
        }
        
        return null;
    }

    public function getTypes() {
        return Calendar\Type::getAll();
    }

    /**
     * @return array
     */
    public function getMonthsList() {

        $months = array();
        for($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        return $months;
    }

    /**
     * @param $month
     * @return array
     */
    public function getDaysList($month, $year) {
        $dayNumber = 1;
        $days = array();

        $numberDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $firstDayNumber = (int) date('w', mktime(0, 0, 0, $month, 1, $year));

        if($firstDayNumber < $dayNumber) {
            $i = 6;
        } elseif($firstDayNumber == $dayNumber) {
            $i = 0;
        } else {
            $i = $firstDayNumber - $dayNumber;
        }

        if(!empty($i)) {
            $days = array_fill(0, $i, null);
        }

        for($i = 1; $i <= $numberDays; $i++) {
            $days[] = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', mktime(0, 0, 0, $month, $i, $year)));;
        }

        if(count($days) < 42) {
            $days = $days + array_fill(count($days), 42 - count($days), null);
        }

        return $days;
    }

    /**
     * @return array
     */
    public function getDaysNames() {
        $result = array();

        $days = array(
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            0 => 'Sun',
        );

        for($i = 0; $i < 6; $i++) {
            $result[$i] = $days;
        }

        return $result;
    }
}
