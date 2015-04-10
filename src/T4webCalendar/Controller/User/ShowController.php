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
        return $this->view;
    }

}
