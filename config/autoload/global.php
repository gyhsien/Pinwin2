<?php
use Pinwin\Mvc\Controller\LazyControllerAbstractFactory;
use Pinwin\View\Helper\Factory\DelegatorFactory;
// use Zend\ServiceManager\Factory\InvokableFactory;

return [
    // Initial configuration with which to seed the ServiceManager.
    // Should be compatible with Zend\ServiceManager\Config.
    'service_manager' => [
        'factories' =>[
            
        ],    
    ],
    
    'controllers' => [
        'abstract_factories' => [LazyControllerAbstractFactory::class],
    ],
    
    'view_helpers' => [        
        'aliases' => [
            'footScript'=> \Pinwin\View\Helper\FootScript::class,
        ],        
        'factories' => [
            \Pinwin\View\Helper\FootScript::class => DelegatorFactory::class,
        ],
    ],    
    
    
    'view_manager'=>[
        'strategies'=>['ViewJsonStrategy', 'ViewFeedStrategy']
    ],
    
    
    'db'=>[
        'adapters'=>[
            'masterDbAdapter'=>[
                'options'=>['buffer_results'=>true],
                'charset'=>'utf8'
            ]
        ]
    ],
];