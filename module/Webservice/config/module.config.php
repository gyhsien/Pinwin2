<?php
namespace Webservice;
return [
    'router' => [
        'routes' => [
            'werservice-captcha' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/webservice/captcha',
                    'defaults' => [
                        '__NAMESPACE__' => __NAMESPACE__,
                        'controller'    => Controller\CaptchaController::class,
                        'action'        => 'index',
                    ],
                ],
            ],            
        ],
    ],
    
    'view_manager' => [
        'template_map' => [
            __NAMESPACE__.'/404'               => __DIR__ . '/../view/error/404.phtml',
            __NAMESPACE__.'/error'             => __DIR__ . '/../view/error/index.phtml',
        ],        
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
