<?php
namespace Pinwin\ModuleManager\Feature;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\EventManager\EventInterface;
use Pinwin\Events\Listener;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Filter\Word\SeparatorToCamelCase;
use PinwinORM\GatewayFactory;
use Zend\Db\Sql\Sql;

abstract class AbstractModule implements BootstrapListenerInterface, ConfigProviderInterface{
    
    
    static protected $config = ['session_containers'=>[]];
    
    /**
     * 
     * @var SeparatorToCamelCase
     */
    protected $wordFilter;
    
    /**
     * 
     * @var \ReflectionObject
     */
    protected $reflection;
    
    public function __construct()
    {
        $this->reflection = new \ReflectionObject($this);
        $this->wordFilter = new SeparatorToCamelCase();
    }
    
    public function onBootstrap(EventInterface $e)
    {
        $events = $e->getApplication()->getEventManager();
        
        $namespace = $this->reflection->getNamespaceName();
        
        $service = $e->getTarget()->getServiceManager();
        $adapter = $service->get(MAIN_DB_ADAPTER);
        $routeAggregate = new Listener\RouteAggregate($namespace);
        $dispatchAggregate = new Listener\DispatchAggregate($namespace);
        $dispatchErrorAggregate = new Listener\DispatchErrorAggregate($namespace);
        $finishAggregate = new Listener\FinishAggregate($namespace);
        
        $routeAggregate->attach($events, 1);
        $dispatchAggregate->attach($events);
        $dispatchErrorAggregate->attach($events);
        $finishAggregate->attach($events);
        
    }
    
    public function getConfig()
    {
        $this->wordFilter->setSeparator('-');
        $sessionContainerIndex = $this->wordFilter->filter(
            $this->reflection->getNamespaceName()
        );
        $this->wordFilter->setSeparator('_');
        $sessionContainerIndex = $this->wordFilter->filter($sessionContainerIndex);
        
        $sessionContainerIndex = lcfirst($sessionContainerIndex);
        $sessionContainerIndex.= "Session";
        
        if(false === array_search(
            $sessionContainerIndex, 
            self::$config['session_containers'])
        ){
            self::$config['session_containers'][] = $sessionContainerIndex;
        }
       
        //var_export(self::$config['session_containers']);
        return self::$config;
    }
    
}