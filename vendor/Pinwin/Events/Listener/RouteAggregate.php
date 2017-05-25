<?php
namespace Pinwin\Events\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
//use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;

class RouteAggregate extends AbstractAggregate{

    public function fixActionListener(EventInterface $e)
    {
        if(!$this->isModuleUse($e)) return ;
        $action = $e->getRouteMatch()->getParam('action');
        $word_filter = new \Zend\Filter\Word\SeparatorToCamelCase();
        $word_filter->setSeparator('-');        
        $action = $word_filter->filter($action);
        $word_filter->setSeparator('_');
        $action = $word_filter->filter($action);
        
        $action = lcfirst($action);
        
        $e->getRouteMatch()->setParam('action', $action);
    }
    
}