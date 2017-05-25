<?php
namespace Frontend;

use Pinwin\ModuleManager\Feature\AbstractModule;
class Module extends AbstractModule
{
    public function getConfig()
    {
         
        $config = array_merge_recursive(
            parent::getConfig(), 
            include __DIR__ . '/../config/module.config.php'
        );
        return $config;
    }
}