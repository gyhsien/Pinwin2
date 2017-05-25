<?php
namespace Board\Controller;

use Pinwin\Mvc\Controller\InjectServiceController;
use Zend\View\Model\ViewModel;

class IndexController extends InjectServiceController
{
    public function indexAction()
    {        
		return new ViewModel();
    }
}
