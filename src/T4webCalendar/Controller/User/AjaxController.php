<?php

namespace T4webCalendar\Controller\User;

use T4webBase\Domain\Service\BaseFinder as FindService;
use T4webBase\Domain\Service\Create as CreateService;
use T4webBase\Domain\Service\Update as UpdateService;
use T4webBase\Domain\Service\Delete as DeleteService;
use T4webBase\InputFilter\InvalidInputError;
use T4webCalendar\Calendar\Type;
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
        UpdateService $updateService,
        DeleteService $deleteService,
        AjaxViewModel $view
    )
    {
        $this->finder = $finder;
        $this->createService = $createService;
        $this->updateService = $updateService;
        $this->deleteService = $deleteService;
        $this->view = $view;
    }

    /**
     * @return AjaxViewModel
     */
    public function typeListAction() {

        $this->view->setFormData(Type::getAll());

        return $this->view;
    }

    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $params = $this->getRequest()->getPost()->toArray();

        if(!isset($params['id'])) {
            $params['id'] = 0;
        }

        /* @var $calendar \T4webCalendar\Calendar\Calendar */
        $calendar = $this->finder->find(['T4webCalendar' => ['Calendar' => [
            'date' => $params['date'],
        ]]]);

        if($calendar && $calendar->getId() != $params['id']) {
            $this->view->setErrors(new InvalidInputError(array('date' => array('recordExists' => "Праздник уже создан"))));
            $this->view->setFormData($params);

            return $this->view;
        }

        if(isset($params['id']) && !empty($params['id'])) {
            $result = $this->updateService->update($params['id'], $params);

            if (!$result) {
                $this->view->setErrors($this->updateService->getErrors());
            }
        } else {
            if(isset($params['id'])) {
                unset($params['id']);
            }

            $result = $this->createService->create($params);

            if (!$result) {
                $this->view->setErrors($this->createService->getErrors());
            } else {
                $params['id'] = $result->getId();
            }
        }

        $this->view->setFormData($params);

        return $this->view;
    }

    public function deleteAction() {
        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $id = $this->getRequest()->getPost('id');

        $result = $this->deleteService->delete($id);

        if (!$result) {
            $this->view->setErrors($this->deleteService->getErrors());
        }

        return $this->view;
    }

}
