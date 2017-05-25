<?php
use Pinwin\Mvc\Controller\LazyControllerAbstractFactory;
use Pinwin\View\Helper\Factory\DelegatorFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    // Initial configuration with which to seed the ServiceManager.
    // Should be compatible with Zend\ServiceManager\Config.
    'service_manager' => [
        'factories' =>[
            
        ],
        'abstract_factories' => [
            
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
    
    /*
     *  參考
     *  https://dojotoolkit.org/documentation/tutorials/1.10/dojo_config/index.html
     *  https://dojotoolkit.org/reference-guide/1.10/loader/amd.html#loader-amd-configuration
     *  https://dojotoolkit.org/reference-guide/1.10/dojo/_base/config.html
     */
    'dojo' => [
        'parseOnLoad' => false,
        'async' => true,
        'has' => [
            'dojo-firebug' => APP_ENV === 'development' ? true : false,
            'dojo-debug-messages'=>APP_ENV === 'development' ? true : false,
        ],
        'cacheBust' => APP_ENV === 'production' ? true : false,
        'packages' =>[
            ["name"=>"dijit", "location"=>"assets/release/dojo/dijit"],
            ["name"=>"dojo", "location"=>"assets/release/dojo/dojo"],
            ["name"=>"dojox", "location"=>"assets/release/dojo/dojox"],
            ["name"=>"pinwin", "location"=>"assets/release/pinwin"],
        ]/*,
        'jquery' => [
            'deps' => true,
            'slow' => [
                [
                    "name" => "jQuery",
                    "location" => "assets/vendor/jquery",
                    "main" => "jquery-1.12.4.min"
                ],
                [
                    "name" => "jquery-migrate",
                    "location" => "assets/vendor/jquery",
                    "main" => "jquery-migrate-1.4.1.min"
                ]
            ],
            'fast' => [
                [
                    "name" => "jQuery",
                    "location" => "assets/vendor/jquery",
                    "main" => "jquery-3.1.1.min"
                ],
                [
                    "name" => "jquery-migrate",
                    "location" => "assets/vendor/jquery",
                    "main" => "jquery-migrate-3.0.0.min"
                ]
            ]
        ],
    */
    
    ],
    
    
];