<?php
namespace Pinwin\Db;
use Zend\Db\Adapter as ZendAdapter; 
use Zend\Db\ConfigProvider as ZendConfigProvider;
class ConfigProvider extends ZendConfigProvider
{

    
/**
     * Retrieve zend-db default dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        
        return [
            'abstract_factories' => [
                Adapter\AdapterAbstractServiceFactory::class,
            ],
            'factories' => [
                ZendAdapter\AdapterInterface::class => ZendAdapter\AdapterServiceFactory::class,
            ],
            'aliases' => [
                ZendAdapter\Adapter::class => ZendAdapter\AdapterInterface::class,
            ],
        ];
    }
}
