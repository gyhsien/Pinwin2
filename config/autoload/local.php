<?php
use Zend\Session;
//use Zend\Session\SaveHandler\SaveHandlerInterface;
use Pinwin\Session\Service\SaveHanlderFactory;

return [
    'service_manager' => [
        'factories' =>[
            SaveHandlerInterface::class => SaveHanlderFactory::class,
        ]
    
    ],
    
    //自定義extensions
    /*
    'extensions' => [
        'dependent'=>'pw_extensions',
        'relashionship'=>'pw_extension_installation',
        'uses' =>[            
            'User'
        ],
    ],
    */
    'db'=>[
        'adapters'=>[
            MAIN_DB_ADAPTER=>[
                'driver'=>'pdo',
                'dsn' => 'mysql:dbname=pimcore_empty;host=127.0.0.1',
                'database'=>'pimcore_empty',
                'username'=>'root',
                'hostname'=>'127.0.0.1',
                'password'=>'1234',
            ],
        ]
    ],
    'session_containers' => [
//         'boardSession',
//         'frontendSession',
//         'webserviceSession'
    ],
    'session_config' => [
        'remember_me_seconds' => 1800,
    ],
    'session_storage' =>[
        'type' => Session\Storage\SessionArrayStorage::class,
        'options' => []
    ],
    'session_savehandler' => [
        'type' => 'cache' ,//cache 、 db 、 mongoDB
        'table' => 'session', //for db type
        'options' => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'cache_dir' => 'data/cache/session',
                    'dir_level' => 0
                ]
            ]
        ]
    ],
    'session_validator' => [
        Session\Validator\RemoteAddr::class,
        Session\Validator\HttpUserAgent::class,
    ],
    'enable_default_container_manager' => true
];
