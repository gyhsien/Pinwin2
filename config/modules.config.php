<?php
$module_config = array_merge([
    'Zend\Cache',
    'Zend\Db',
    'Zend\Filter',
    'Zend\Form',
    'Zend\I18n',
    'Zend\Log',
    'Zend\Mail',
    'Zend\Router',
    'Zend\Navigation',
    'Zend\Paginator',
    'Zend\Session',
    'Zend\Validator'
], require 'url_modules_name.php');

return $module_config;