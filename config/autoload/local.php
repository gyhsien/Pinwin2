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
    
    'db'=>[
        'adapters'=>[
            MAIN_DB_ADAPTER=>[
                'driver'=>'pdo_mysql',
                'database'=>'pinwin2',
                'username'=>'root',
                'hostname'=>'127.0.0.1',
                'password'=>'1234',
            ],
        ]
    ],
    'session_containers' => [],
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
