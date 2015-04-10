<?php

namespace T4webCalendar\Controller\User;

use T4webBase\Domain\Service\BaseFinder as FindService;
use T4webBase\Domain\Service\Create as CreateService;
use T4webBase\Domain\Service\Delete as DeleteService;
use T4webCalendar\Controller\ViewModel\AjaxViewModel;

use Zend\Mvc\Controller\AbstractActionController;

class AjaxController extends AbstractActionController
{

    /**
     * @var CreateService
     */
    private $createService;

    /**
     * @var DeleteService
     */
    private $deleteService;

    /**
     * @var FindService
     */
    private $finder;

    /**
     * @var AjaxViewModel
     */
    private $view;


    public function __construct(
        FindService $finder,
        CreateService $createService,
        DeleteService $deleteService,
        AjaxViewModel $view
    )
    {
        $this->finder = $finder;
        $this->createService = $createService;
        $this->deleteService = $deleteService;
        $this->view = $view;
    }

    public function defaultAction() {
        /* @var $calendar \T4webCalendar\Calendar\CalendarCollection */
        $calendar = $this->finder->findMany();

        $this->view->setFormData($calendar);

        return $this->view;
    }

    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $params = $this->getRequest()->getPost()->toArray();

        /* @var $calendar \T4webCalendar\Calendar\Calendar */
        $calendar = $this->finder->find(['T4webCalendar' => ['Calendar' => [
            'date' => $params['date'],
        ]]]);

        if($calendar !== null) {
            $result = $this->deleteService->delete($calendar->getId());
        } else {
            $result = $this->createService->create($params);
        }

        if (!$result) {
            $this->view->setErrors($this->createService->getErrors());
        }

        $this->view->setFormData($params);

        return $this->view;
    }

}
