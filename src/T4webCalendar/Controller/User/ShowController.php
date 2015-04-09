<?php

namespace T4webCalendar\Controller\User;

use Zend\Mvc\Controller\AbstractActionController;

use T4webBase\Domain\Service\BaseFinder;

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
        /** @var $calendar CalendarCollection */
        $calendar = $this->finder->findMany();

        $this->view->setCalendar($calendar);

        return $this->view;
    }

}
