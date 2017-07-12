<?php
namespace Pinwin\ModuleManager\Feature;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\EventManager\EventInterface;
use Zend\Filter\Word\SeparatorToCamelCase;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Db\Adapter\Adapter;
use PinwinORM\Pinwin2\PagesTable;
use Pinwin\Events\Listener;
use Zend\Code\Reflection\ClassReflection;

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
    
    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        
        // Registering a listener at default priority, 1, which will trigger
        // after the ConfigListener merges config.
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'));
    }
    
    public function onMergeConfig(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config         = $configListener->getMergedConfig(false);
        
        $dbConfig = $config['db']['adapters'][MAIN_DB_ADAPTER];
        $adapter = new Adapter($dbConfig);
        $db_name = $dbConfig['database'];
        
        
        $this->wordFilter->setSeparator('-');
        $db_name = $this->wordFilter->filter($db_name);
        $this->wordFilter->setSeparator('_');
        $db_name = $this->wordFilter->filter($db_name);
        
        $routerTableReflection = new ClassReflection('\\PinwinORM\\'.$db_name.'\\RouterTable');        
        $routerTable = $routerTableReflection->newInstance($adapter);
        
        $pageTableReflection = new ClassReflection('\\PinwinORM\\'.$db_name.'\\PagesTable');
        $pageTable = $pageTableReflection->newInstance($adapter);
        
        
        //$routerTableReflection->getConstant('TABLE')
        $routerSelect = $routerTable->select([PREFIX_TABLE.'pages.module' =>$this->reflection->getNamespaceName()], true);
        $routerSelect->join(
            $pageTable::TABLE,
            $routerTable::TABLE.'.pages_id='.$pageTable::TABLE.'.id',
            ['module']);
        $pagesResultSet = $routerTable->selectWith($routerSelect);
        if($pagesResultSet)
        {
            foreach ($pagesResultSet as $current)
            {
                $config['router']['routes'] = array_merge_recursive($config['router']['routes'], json_decode($current['options'], true));
            }
        }else{
            return ;
        }
        
        $adapter->driver->getConnection()->disconnect();
        
        // Pass the changed configuration back to the listener:
        $configListener->setMergedConfig($config);
        
    }
    
    
    
    public function onBootstrap(EventInterface $e)
    {
        $events = $e->getApplication()->getEventManager();
        
        $namespace = $this->reflection->getNamespaceName();
        
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