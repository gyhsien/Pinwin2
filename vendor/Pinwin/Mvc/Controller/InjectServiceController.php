<?php
namespace Pinwin\Mvc\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
abstract class InjectServiceController extends AbstractActionController{
    
    /**
     *
     * @var ServiceManager
     */
    protected $container;
    
    public function __construct(ServiceManager $container)
    {
        $this->container = $container;
    }
    
}