<?php

namespace T4webCalendar\Controller\User;

use Zend\Mvc\Controller\AbstractActionController;

class AjaxController extends AbstractActionController
{

    /**
     * @var AjaxViewModel
     */
    private $view;


    public function __construct(AjaxViewModel $view)
    {
        $this->view = $view;
    }

    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $params = $this->getRequest()->getPost()->toArray();

        $this->view->setFormData($params);

        return $this->view;
    }

}
