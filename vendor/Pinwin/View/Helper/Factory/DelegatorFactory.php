<?php
namespace Pinwin\View\Helper\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class DelegatorFactory implements FactoryInterface{
    public function __invoke(ContainerInterface $container = null, $requestedName = null, array $options = null)
    {
        $params = [];
        
        if($container instanceof ContainerInterface)
        {
            $params[] = $container;
        }
        
        if(is_array($options))
        {
            $params[] = array();
        }
        
        return new $requestedName(...$params);
    }
}