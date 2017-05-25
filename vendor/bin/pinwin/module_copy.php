<?php
//php vendor/pinwin/bin/module_copy.php r88Main r88Vn

use Zend\Filter\Word\CamelCaseToDash;
use Zend\Filter\Word\DashToCamelCase;
use Zend\Code\Generator\FileGenerator;
use Pinwin\Tools\Tools;

$basePath = dirname(dirname(dirname(__DIR__)));
ini_set('error_log', 'vendor/pinwin/bin/php_errors_'.date("Ymd").'.log');
require 'vendor/autoload.php';
require 'vendor/Pinwin/Tools/Tools.php';

$camelCaseToDash = new CamelCaseToDash();
$dashToCamelCase = new DashToCamelCase();
$fromModule = strtolower($camelCaseToDash->filter($argv[1]));
$fromModuleNamespace = $dashToCamelCase->filter($fromModule);
$goalModule = strtolower($camelCaseToDash->filter($argv[2]));
$goalModuleNamespace = $dashToCamelCase->filter($goalModule);

//step1 先把整個資料夾複製過去

Tools::copy(
    './module/'.$fromModuleNamespace, 
    './module/'.$goalModuleNamespace, 
    true
);

//step2 修改config/module.config.php 的namespace
$goal_module_config = file_get_contents('./module/'.$goalModuleNamespace.'/config/module.config.php');
$goal_module_config = str_replace($fromModule, $goalModule, $goal_module_config);
$goal_module_config = str_replace($fromModuleNamespace, $goalModuleNamespace, $goal_module_config);
//echo $goal_module_config;
if(file_put_contents('./module/'.$goalModuleNamespace.'/config/module.config.php', $goal_module_config))
{
    echo "[Build success:] The file \"$goalModuleNamespace/config/module.config.php\" was replaced successfully.\n";
}else{
    echo "[Build failed:] The file \"$goalModuleNamespace/src/module.php\" was replaced failed.\n";
}

//step3 修改src/module.php 的namespace
$goal_module = file_get_contents('./module/'.$goalModuleNamespace.'/src/module.php');
$goal_module = str_replace($fromModuleNamespace, $goalModuleNamespace, $goal_module);
if(file_put_contents('./module/'.$goalModuleNamespace.'/src/module.php', $goal_module))
{
    echo "[Build success:] The file \"$goalModuleNamespace/src/module.php\" was replaced successfully.\n";
}else{
    echo "[Build failed:] The file \"$goalModuleNamespace/src/module.php\" was replaced failed.\n";
}

//step3 更新所有Controller的namespace
$folder = './module/'.$goalModuleNamespace.'/src/controller/';
$iterator = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator(
        $folder,
        \RecursiveDirectoryIterator::SKIP_DOTS),
    \RecursiveIteratorIterator::SELF_FIRST
);
foreach ($iterator as $item)
{
    if($item->isFile() && $item->getExtension() == 'php')
    {
        $generator = FileGenerator::fromReflectedFileName(
            $folder.$item->getFilename());
        $generator->setNamespace($goalModuleNamespace);
        
        $goalPath = $folder.$item->getFilename();
        if(file_put_contents($goalPath, $goal_module))
        {
            echo "[Build success:] The file \"$goalPath\" was replaced successfully.\n";
        }else{
            echo "[Build failed:] The file \"$goalPath\" was replaced failed.\n";
        }
        
    }
}

//更新module config
require 'vendor/pinwin/bin/class/pinwin/bin_module.php';
pinwin/bin_module::addModuleGlobaleconfig('./module/'.$goalModuleNamespace, $goalModuleNamespace);

//$generator = FileGenerator::fromReflectedFileName();

/*
var_export([
    $fromModule,
    './module/'.$fromModule,
    $fromModuleNamespace,
    $goalModule,
    './module/'.$goalModule,
    $goalModuleNamespace
]);
*/