<?php
//php vendor/bin/pinwin/documents/page.php --config=masterDbAdapter --module=Frontend
use Zend\Db\Adapter\Adapter as DbAdapter;

if(!defined('MAIN_DB_ADAPTER'))
{
    require 'config/constants.php';
}


$start = time();

$basePath = dirname(dirname(dirname(__DIR__)));
ini_set('error_log', 'vendor/pinwin/bin/php_errors_'.date("Ymd").'.log');
/** @var $loader \Composer\Autoload\ClassLoader */
$loader = require 'vendor/autoload.php';
try {
    $config = [
        'options'=>['buffer_results'=>true],
        'charset'=>'utf8'
    ];
    for($i=1 ; $i < count($argv) ; $i++)
    {
        $str = explode('=', preg_replace("/^\-\-/", '', $argv[$i]));
        $config[$str[0]] = preg_replace("/\'|\"/", '', $str[1]);
        //echo $str[0]."\n";
    }
    $module = $config['module'];
    unset($config['module']);
    
    $loader->setPsr4($module.'\\', 'module/'.$module.'/src');
    $loader->register(true);
    
    if(
        (empty($config['driver']) ||
            empty($config['database']) ||
            empty($config['username']) ||
            empty($config['hostname']) ||
            empty($config['password']))  && $config['config']
        
        )
    {
        $index = $config['config'];
        $systemConfig = require 'config/autoload/local.php';
        $config = array_merge($config, $systemConfig['db']['adapters'][$index]);
        unset($config['config']);
    }
    
    $dbAdapter = new DbAdapter($config);
    $Directory = new \RecursiveDirectoryIterator('module/'.$module.'/src/Controller');
    $Iterator = new \RecursiveIteratorIterator($Directory);
    $insert_datas = [];
    $wordFilter = new \Zend\Filter\Word\CamelCaseToDash();
    $_module = lcfirst($wordFilter->filter($module));
    $insertDatas = [];
    foreach ($Iterator as $fileinfo)
    {
        /** @var $fileinfo \SplFileInfo */
        if($fileinfo->getFilename() != '.' && $fileinfo->getFilename() != '..')
        {
            $full_class = '\\'.$module.'\\Controller\\'.str_replace('.php', '', $fileinfo->getFilename());
            $reflection = new \ReflectionClass($full_class);
            $namespace = $reflection->getNamespaceName();
            $controller = str_replace(
                'Controller', '', 
                str_replace($namespace.'\\', '', $reflection->getName())
            );
            $controller = strtolower($wordFilter->filter($controller)).PHP_EOL;
            
            $reglectionMethods = $reflection->getMethods(
                ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_FINAL);
            foreach($reglectionMethods as $reflectionMethod)
            {
                /** @var $reflectionMethod ReflectionMethod */
                $methodName = $reflectionMethod->getName();
                if(preg_match('/Action$/', $methodName))
                {
                    $action = strtolower($wordFilter->filter(
                        str_replace('Action', '', $methodName)
                    ));
                    
                    //過濾例外
                    if($action != 'get-method-from' && $action != 'not-found')
                    {
                        $insertDatas[] = sprintf(
                            '(\'%s\', \'%s\', \'%s\')',
                            $_module,
                            trim($controller, "\n\r"),
                            trim($action, "\n\r")
                        );
                    }
                }
            }
            
        }
    }
    $sqlString = 'INSERT INTO `documents_page` (`module`, `controller`, `action`) VALUES '.PHP_EOL;    
    $sqlString .= implode(','.PHP_EOL, $insertDatas);
    $dbAdapter->query($sqlString)->execute();
    echo $sqlString.PHP_EOL.'excute success.';
} catch (Exception $e) {
    echo $e->getMessage();
}