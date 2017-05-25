<?php

use Pinwin\Mvc\Controller\InjectServiceController;
use Zend\View\Model;

class Controller extends InjectServiceController
{
    public function indexAction()
    {                
        return new Model\JsonModel();
    }
}
