<?php
namespace Pinwin\Events\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;

class DispatchErrorAggregate extends AbstractAggregate{
    
    public function ErrorListener(EventInterface $e){
        if(!$this->isModuleUse($e)) return ;
        
        $e->getViewModel()->setTerminal(true);
        
        switch ($e->getError()) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                $e->getViewModel()->setTemplate(
                $this->moduleNamespace.'/404'
                    );
                break;
            case Application::ERROR_EXCEPTION:
            default:
    
                $e->getViewModel()->setVariables([
                'message'            => 'An error occurred during execution; please try again later.',
                'exception'          => $e->getParam('exception'),
                'display_exceptions' => true,
                ]);
    
                $e->getViewModel()->setTemplate(
                    $this->moduleNamespace.'/error'
                    );
                break;
        }
    }
    
}