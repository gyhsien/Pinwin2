<?php
namespace Pinwin\Session;

use Zend\Session\SessionManager;
//use Zend\Session\ValidatorChain;
use Zend\Session\Container;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SaveHandler;
use Zend\Session\Storage\Factory as storageFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Storage\Factory;
use Zend\Cache\StorageFactory as cacheStorageFactory;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;


abstract class SessionAssist {
    
    /**
     *
     * @var ServiceManager
     */
    static protected $serviceLocator;
    
    /**
     * 
     * @var Container
     */
    static private $container;
    
    /**
     * 
     * @var SessionManager
     */
    static private $manager;

    
    /**
     * 
     * @param ServiceManager $serviceLocator
     * @return ServiceManager
     */
    static public function init(ServiceManager $serviceLocator, $name='pinwin')
    {
        if(!self::$serviceLocator instanceof ServiceManager)
        {
            self::$serviceLocator = $serviceLocator;
            self::initSession($name);
        }
        return self::$serviceLocator;
    }
    
    /**
     * 
     * @return \Zend\Session\Container
     */
    static public function session()
    {
        return self::$container;
    }
    
    
    static protected function initSession($name='pinwin')
    {
        $options = self::getOptions();
        
        $config = null;
        $storage = null;
        $saveHandler = null;
        $validators = [];
        
        if(isset($options['session_config']))
        {
            $config = new SessionConfig();
            $config->setOptions($options['session_config']);
        }
        
        if(isset($options['session_storage']))
        {
            $type = $options['session_storage']['type'];
            $storage_options = 
                isset($options['session_storage']['options']) 
                ? $options['session_storage']['options']
                : [];
            $storage = storageFactory::factory($type, $storage_options);
        }
        
        
        if(isset($options['session_save_handler']))
        {
            $type = strtolower($options['session_save_handler']['type']);
            
            switch ($type)
            {
                case 'cache':
                    $saveHandler = new SaveHandler\Cache(
                        cacheStorageFactory::factory(
                            $options['session_save_handler']['options']
                        )
                    );
                    echo '';
                    break;
                case 'db':
                    $adapter = GlobalAdapterFeature::getStaticAdapter();
                    $tableGateway = new TableGateway(
                        $options['session_save_handler']['table'], $adapter
                    );
                    $hanlderOptions = 
                        isset($options['session_save_handler']['options']) 
                        ? $options['session_save_handler']['options'] 
                        : null;
                    $dbGatewayOptions = new SaveHandler\DbTableGatewayOptions(
                        $hanlderOptions);
                    $saveHandler = new SaveHandler\DbTableGateway(
                        $tableGateway, $dbGatewayOptions);
                    break;
                case 'mongodb':
                    $mongoClient = new \Mongo\Client();
                    $mongoDbOptions = new SaveHandler\MongoDBOptions(
                        $options['session_save_handler']['options']);
                    $saveHandler = new SaveHandler\MongoDB($mongoClient, 
                        $options);
                    break;
            }
        }
        
        if(isset($options['session_validator']))
        {
            $validators = $options['session_validator'];
        }

        if(isset($options['session_options']))
        {
            $options = $options['session_options'];
        }
        
        
        self::$manager = new SessionManager($config, $storage, $saveHandler, 
            isset($options['options']) ? $options['options'] : []);

        self::$manager->start();
        
        if (isset($options['enable_default_container_manager'])
            && $options['enable_default_container_manager']
        ) {
                Container::setDefaultManager(self::$manager);
        }
        
        self::$container = new Container($name='pinwin');
    }
    
    static protected function getOptions()
    {
        $tmp = self::$serviceLocator->get('config');
        return $tmp['session_assit'];
    }
}