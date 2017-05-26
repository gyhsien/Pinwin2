<?php
namespace Pinwin\Extensions;
use Pinwin\Events\Listener\AbstractAggregate;
//use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Filter\Word\SeparatorToCamelCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Session\Container as SessionContainer;

class ExtensionAggregate extends AbstractAggregate
{
    
    /**
     * 
     * @var []
     */
    protected $mvcEvents = [];
    
    /**
     * 
     * @var Adapter
     */
    //protected $dbAdapter;
    
    public function __construct($moduleName='')
    {
        parent::__construct($moduleName);
        
        //$this->dbAdapter = GlobalAdapterFeature::getStaticAdapter();
        
        $wordFilter = new SeparatorToCamelCase('.');
        $this->mvcEvents = [
            //Bootstrap
            $wordFilter->filter(MvcEvent::EVENT_BOOTSTRAP) => MvcEvent::EVENT_BOOTSTRAP,
        
            //Dispatch
            $wordFilter->filter(MvcEvent::EVENT_DISPATCH) => MvcEvent::EVENT_DISPATCH,
        
            //DispatchError
            $wordFilter->filter(MvcEvent::EVENT_DISPATCH_ERROR) => MvcEvent::EVENT_DISPATCH_ERROR,
        
            //Finish
            $wordFilter->filter(MvcEvent::EVENT_FINISH) => MvcEvent::EVENT_FINISH,
        
            //Render
            $wordFilter->filter(MvcEvent::EVENT_RENDER) => MvcEvent::EVENT_RENDER,
        
            //RenderError
            $wordFilter->filter(MvcEvent::EVENT_RENDER_ERROR) => MvcEvent::EVENT_RENDER_ERROR,
        
            //Route
            $wordFilter->filter(MvcEvent::EVENT_ROUTE) => MvcEvent::EVENT_ROUTE
        ];
        
    }
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        
        //var_export($events->getSharedManager());
        foreach( $this->reflaction->getMethods(\ReflectionMethod::IS_PUBLIC) as $method )
        {            
            $methodName = $method->getName();
            $matches = [];
            preg_match('/(BOOTSTRAP|ERROR|DISPATCH|FINISH|RENDER|ROUTE)$/i', $methodName, $matches);
            
            if(count($matches) > 0)
            {
                $eventName = $matches[0];
                $this->listeners[] = $events->attach($this->mvcEvents[$eventName], [$this, $methodName]);
            }
        
        }
    
    }
    
    /**
     * 
     * @param SessionContainer $sessionContainer
     */
    public function initSessionExtends(SessionContainer $sessionContainer)
    {
        if(empty($sessionContainer->extends))
        {
            $sessionContainer->extends = [];
        }
    }
    
    
}