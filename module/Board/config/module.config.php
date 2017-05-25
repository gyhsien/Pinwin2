<?php
namespace Board;

return [
    'router' => [
        'routes' => [
            'board' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/admin[/:controller][/:action]',
                    'defaults' => [
                        '__NAMESPACE__' => __NAMESPACE__,
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'index',
                    ],
                ],
            ],            
        ],
    ],
    
    'view_manager' => [
        'doctype'                  => 'HTML5', 
        'not_found_template'       => __NAMESPACE__.'/404', 
        'exception_template'       => __NAMESPACE__.'/error', 
        'template_map' => [
            __NAMESPACE__.'/404'               => __DIR__ . '/../view/error/404.phtml',
            __NAMESPACE__.'/error'             => __DIR__ . '/../view/error/index.phtml',            
            __NAMESPACE__.'/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
