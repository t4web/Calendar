<?php

namespace T4webCalendar\View\Helper;

use DateTime;
use Zend\View\Helper\AbstractHelper;

class CalendarYearPaginator extends AbstractHelper {
    
    public function __invoke(DateTime $current) {

        $view = new CalendarYearPaginatorViewModel();
        $view->setCurrent($current);

        return $this->getView()->render($view);
    }
}