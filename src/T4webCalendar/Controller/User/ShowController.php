<?php

namespace T4webCalendar\Controller\User;

use Zend\Mvc\Controller\AbstractActionController;
use T4webBase\Domain\Service\BaseFinder;
use T4webCalendar\Controller\ViewModel\ShowViewModel;

class ShowController extends AbstractActionController
{

    /**
     * @var BaseFinder
     */
    private $finder;

    /**
     * @var ShowViewModel
     */
    private $view;

    public function __construct(BaseFinder $finder, ShowViewModel $view)
    {
        $this->finder = $finder;
        $this->view = $view;
    }

    /**
     * @return ShowViewModel
     */
    public function defaultAction()
    {
        $year = $this->params('year', date('Y'));
        $currentDate = \DateTime::createFromFormat('Y-m-d', $year . '-01-01');

        $this->view->setCurrent($currentDate);

        /* @var $calendar \T4webCalendar\Calendar\CalendarCollection */
        $calendar = $this->finder->findMany(['T4webCalendar' => ['Calendar' => [
            'dateFrom' => $currentDate->format("Y-m-d"),
            'dateTo' => $currentDate->modify('+1 year -1 day')->format("Y-m-d"),
            'orderBy' => 'date',
        ]]]);

        $this->view->setCalendar($calendar);

        return $this->view;
    }

}
