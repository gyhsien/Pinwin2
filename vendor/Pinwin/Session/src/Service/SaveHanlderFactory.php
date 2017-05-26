<?php
namespace Pinwin\Session\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\Session\Container;
use Zend\Session\SaveHandler;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Storage\Factory;
use Zend\Cache\StorageFactory as cacheStorageFactory;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;


class SaveHanlderFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $saveHandler = null;
        if (! isset($config['session_savehandler']) || ! is_array($config['session_savehandler'])) {
            throw new ServiceNotCreatedException('Configuration is missing a "session_savehanlder" key, or the value of that key is not an array');
        }
        $config = $config['session_savehandler'];
        
        $type = $config['type'];
        $options = isset($config['options']) ? $config['options'] : [];
        
        switch ($type) {
            case 'cache':
                $saveHandler = new SaveHandler\Cache(cacheStorageFactory::factory($options));
                break;
            case 'db':
                $adapter = GlobalAdapterFeature::getStaticAdapter();
                $tableGateway = new TableGateway($config['table'], $adapter);
                $hanlderOptions = isset($options) ? $options : null;
                $dbGatewayOptions = new SaveHandler\DbTableGatewayOptions($hanlderOptions);
                $saveHandler = new SaveHandler\DbTableGateway($tableGateway, $dbGatewayOptions);
                break;
            case 'mongodb':
                $mongoClient = new \Mongo\Client();
                $mongoDbOptions = new SaveHandler\MongoDBOptions($options);
                $saveHandler = new SaveHandler\MongoDB($mongoClient, $mongoDbOptions);
                break;
        }
        return $saveHandler;
    }
}