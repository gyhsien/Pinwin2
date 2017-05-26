<?php
namespace Pinwin\Db\Adapter;

use Zend\Db\Adapter\AdapterAbstractServiceFactory as ZendAdapterAbstractServiceFactory;
use Interop\Container\ContainerInterface;

class AdapterAbstractServiceFactory extends ZendAdapterAbstractServiceFactory 
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container);
        return new Adapter($config[$requestedName]);
    }
    
}