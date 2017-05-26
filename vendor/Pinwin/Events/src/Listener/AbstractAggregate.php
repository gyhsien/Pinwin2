<?php
namespace Pinwin\Events\Listener;

use Zend\EventManager\EventManagerInterface;
//use Zend\EventManager\EventManager\EventInterface;
use Zend\EventManager\AbstractListenerAggregate as ZendAbstractListenerAggregate;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;
use Zend\Filter\Word\CamelCaseToSeparator;

abstract class AbstractAggregate extends ZendAbstractListenerAggregate 
{
    
    protected $moduleNamespace;
    
    /**
     * 
     * @var \ReflectionObject
     */
    protected $reflaction;
    
    public function __construct($moduleNamespace='')
    {
        $this->reflaction = new \ReflectionObject($this);
        $this->moduleNamespace = $moduleNamespace;
    }
    
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        
        $methodName = str_replace($this->reflaction->getNamespaceName().'\\', '', $this->reflaction->getName());
        $methodName = str_replace('Aggregate', '', $methodName);
        $filter = new CamelCaseToSeparator('.');
        $methodName = strtolower($filter->filter($methodName));
        
        foreach( $this->reflaction->getMethods(\ReflectionMethod::IS_PUBLIC) as $listenerMethod )
        {
            $listenerName = $listenerMethod->getName();
            if( preg_match("/listener$/i", $listenerName) )
            {
                $this->listeners[] = $events->attach($methodName, [$this, $listenerMethod->getName()], $priority);
            }
        }
    
    }
    
    public function isModuleUse(MvcEvent $e)
    {
        $matches = $e->getRouteMatch();
        
        if($matches instanceof RouteMatch)
        {
            if(!isset($matches->getParams()['__NAMESPACE__']))
            {
                //$e->stopPropagation(true);
                return false;
            }
            
            $namepsaceParam = $matches->getParams()['__NAMESPACE__'];
            
            
            
            if($matches instanceof RouteMatch)
            {
                if($namepsaceParam !== $this->moduleNamespace)
                {
                    //$e->stopPropagation(true);
                    return false;
                }
            }
            
        }
        return true;
    }
    
}