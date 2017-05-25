<?php
namespace Frontend\Controller;

use Zend\View\Model;
use Pinwin\Mvc\Controller\InjectServiceController;

class IndexController extends InjectServiceController
{

    protected $sub_set = [];
    
    public function indexAction()
    {        
		return new Model\ViewModel();
    }

    
    public function loginAction()
    {
        return new Model\ViewModel();
    }
}
