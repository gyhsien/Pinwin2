<?php
namespace Frontend;

return [
    'router' => [
        'routes' => [],
    ],   
    'view_manager' => [
        'display_not_found_reason' => (APP_ENV === 'production') ? false : true,
        'display_exceptions'       => (APP_ENV === 'production') ? false : true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',        
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
