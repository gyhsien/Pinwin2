<?php
namespace Frontend;

use Zend\Router\Http\Literal;
//use Zend\Router\Http\Regex;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        '__NAMESPACE__' => __NAMESPACE__,
                        'controller' => 'index',
                        'action'     => 'index',
                    ],
                ],
            ],
            
            
            'mainuri'=>[
                'type'    => 'segment',
                'options' => [
                    'route'    => '/:controller[/:action]',
                    'defaults' => [
                        '__NAMESPACE__' => __NAMESPACE__,
                        'action'        => 'index',
                    ],
                ],
                
            ],
            
            'moduleuri'=>[
                'type'    => 'segment',
                'options' => [
                    'route'    => '/frontend/:controller[/:action]',
                    'defaults' => [
                        '__NAMESPACE__' => __NAMESPACE__,
                        'action'        => 'index',
                    ],
                ],
            
            ],
            
            /*//靜態頁面
            'content'=>[
                'type' => Regex::class,
                'options'=>[
                    //'regex' => '/(?<id>[a-zA-Z0-9_-]+)\.php|html',
                    'regex' => '/(?<id>[a-zA-Z0-9_-]+)(\.(?<format>(php|json|html|xml|rss)))?',
                    #'regex' => '/(?<lang>[a-zA-Z_-]+)/(?<id>[a-zA-Z0-9_-]+)\.php|html', //多語
                    'defaults' => [
                        '__NAMESPACE__' => __NAMESPACE__,
                        'controller' => Controller\ContentController::class,
                        'action' => 'index',
                    ],
                    'spec' => '/%id%.%format%',
                ],
            ] 
            */ 
        ],
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
