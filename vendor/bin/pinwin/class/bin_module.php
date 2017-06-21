<?php
namespace Pinwin;

use Zend\Code\Generator;
use Pinwin\Tools\Tools;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;

class bin_module
{
    public static function word_filter($wordFilter, $word)
    {
    
        $wordFilter->setSeparator('-');
        $word = $wordFilter->filter($word);
        $wordFilter->setSeparator('_');
        return $wordFilter->filter($word);
    }
    
    public static function makeAllFolders($moduleDir)
    {
        $configFolder = $moduleDir.'/config';
        $controllerFolder = $moduleDir.'/src/Controller';
        $viewFolder = $moduleDir.'/view';
    
        if(!is_dir($configFolder))
        {
            echo "Create folder ".$configFolder.".\n";
            Tools::mkdir_r($configFolder);
        }
    
        if(!is_dir($controllerFolder))
        {
            echo "Create folder ".$controllerFolder.".\n";
            Tools::mkdir_r($controllerFolder);
        }
    
        if(!is_dir($viewFolder))
        {
            echo "Create folder ".$viewFolder.".\n";
            Tools::mkdir_r($viewFolder);
        }
    
        if(!is_dir($viewFolder.'/error'))
        {
            echo "Create folder ".$viewFolder."/error.\n";
            Tools::mkdir_r($viewFolder.'/error');
            copy(
                'library/bin/templates/_module_/view/error/404.phtml',
                $viewFolder.'/error/404.phtml'
                );
            copy(
                'library/bin/templates/_module_/view/error/index.phtml',
                $viewFolder.'/error/index.phtml'
                );
        }
    
        if(!is_dir($viewFolder.'/layout'))
        {
            echo "Create folder ".$viewFolder."/layout.\n";
            Tools::mkdir_r($viewFolder.'/layout');
            copy(
                'library/bin/templates/_module_/view/layout/layout.phtml',
                $viewFolder.'/layout/layout.phtml'
                );
        }
    }
    public static function addModuleFile($moduleDir, $moduleName)
    {
        $filename = $moduleDir.'/src/Module.php';
        if(!is_file($filename))
        {
            $moduleFileGenerator = new Generator\FileGenerator();
            $moduleFileGenerator->setNamespace($moduleName);
            $moduleFileGenerator->setUses(
                ['Pinwin\ModuleManager\Feature\AbstractModule']
                );
            $moduleClassGenerator = new Generator\ClassGenerator();
            $moduleClassGenerator->setExtendedClass('AbstractModule');
            $moduleClassGenerator->setName('Module');
            $moduleClassGenerator->addMethod(
                'getConfig',
                [],
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return include __DIR__ . \'/../config/module.config.php\';'
                );
            $moduleFileGenerator->setClass($moduleClassGenerator);
    
            if(file_put_contents($filename, $moduleFileGenerator->generate()))
            {
                echo "[Build success:] The file \"$filename\" was created successfully.\n";
            }else{
                echo "[Build failed:] The file \"$filename\" was created failed.\n";
            }
        }else{
            echo "The file \"$filename\" already exists.\n";
        }
    }
    
    public static function addModuleLocalconfig($moduleDir, $moduleName)
    {
        $filename = $moduleDir.'/config/module.config.php';
        if(!is_file($filename))
        {
            $data = file_get_contents('library/bin/templates/_module_/config/module.config.php');
            $data = str_replace('%module_name%', $moduleName, $data);
            $wordFilter = new CamelCaseToDash();
            $data = str_replace(
                '%module_url%',
                strtolower($wordFilter->filter($moduleName)),
                $data
                );
    
            if(file_put_contents($filename, $data))
            {
                echo "[Build success:] The file \"$filename\" was created successfully.\n";
            }else{
                echo "[Build failed:] The file \"$filename\" was created failed.\n";
            }
        }else{
            echo "The file \"$filename\" already exists.\n";
        }
    }
    
    public static function addModuleGlobaleconfig($moduleDir, $moduleName)
    {
        $namesValue = require 'config/url_modules_name.php';
    
        $valueGenerator = new Generator\ValueGenerator();
        $fileGenerator = new Generator\FileGenerator();
         
        if(false === array_search($moduleName, $namesValue))
        {
            $namesValue[] = $moduleName;
    
            $valueGenerator->setValue($namesValue);
            $valueGenerator->setType(Generator\ValueGenerator::TYPE_ARRAY_SHORT);
            $valueGenerator->setOutputMode(
                Generator\ValueGenerator::OUTPUT_MULTIPLE_LINE);
    
            $fileGenerator->setBody(trim('return '.$valueGenerator->generate().";", "\r"));
    
            file_put_contents('config/url_modules_name.php', $fileGenerator->generate());
        }
    
        $classMapValue = require 'config/url_modules.php';
        if(empty($classMapValue[$moduleName.'\\']))
        {
            $classMapValue[$moduleName.'\\'] = './'.$moduleDir.'/src';
    
            $valueGenerator->setValue($classMapValue);
            $valueGenerator->setType(Generator\ValueGenerator::TYPE_ARRAY_SHORT);
            $valueGenerator->setOutputMode(
                Generator\ValueGenerator::OUTPUT_MULTIPLE_LINE);
    
            $fileGenerator->setBody(trim('return '.$valueGenerator->generate().";", "\r"));
            file_put_contents('config/url_modules.php', $fileGenerator->generate());
        }
    }
    
    public static function addModuleController($moduleDir, $moduleName, $controllerName)
    {
        $filename = $moduleDir.'/src/Controller/'.$controllerName.'Controller.php';
        if(!is_file($filename))
        {
            $controllerFileGenerator = new Generator\FileGenerator();
            $controllerFileGenerator->setNamespace($moduleName.'\\Controller');
            $controllerFileGenerator->setUses([
                'Zend\View\Model',
                'Pinwin\Mvc\Controller\InjectServiceController'
            ]);
            $controllerClassGenerator = new Generator\ClassGenerator();
            $controllerClassGenerator->setExtendedClass('InjectServiceController');
            $controllerClassGenerator->setName($controllerName.'Controller');
            $controllerFileGenerator->setClass($controllerClassGenerator);
            if(file_put_contents($filename, $controllerFileGenerator->generate()))
            {
                echo "[Build success:] The file \"$filename\" was created successfully.\n";
            }else{
                echo "[Build failed:] The file \"$filename\" was created failed.\n";
            }
        }else{
            echo "The file \"$filename\" already exists.\n";
        }
    }
    
    public static function addModuleControllerAction($moduleDir, $moduleName, $controllerName, $actionName, $view)
    {
        $filename = $moduleDir.'/src/Controller/'.$controllerName.'Controller.php';
        $fileGenerator = FileGenerator::fromReflectedFileName($filename);
        $classGenerator = $fileGenerator->getClass($controllerName.'Controller');
    
        if(!$classGenerator->getMethod($actionName.'Action'))
        {
            $wordFilter = new CamelCaseToDash();
            $moduleFolder = strtolower($wordFilter->filter($moduleName));
            $controllerFolder = $wordFilter->filter($controllerName);
    
            $body = 'return new Model\\';
            $viewPath = '';
            if($view === 'html'){
                $body.= 'ViewModel();';
                $viewPath = $moduleDir.'/view/'.$moduleFolder.'/';
                Tools::mkdir_r($viewPath);
                if($actionName == 'index')
                {
                    $viewPath.= strtolower($wordFilter->filter($controllerName)).'.phtml';
                }else{
                    $viewPath.= $controllerFolder.'/';
                    Tools::mkdir_r($viewPath);
                    $viewPath.= strtolower($wordFilter->filter($actionName)).'.phtml';
                }
    
                if(file_put_contents(
                    $viewPath,
                    '<!--'.$controllerName.'/'.$actionName.'-->'
                    ))
                {
                    echo "[Build success:] The file \"$viewPath\" was created successfully.\n";
                }else{
                    echo "[Build failed:] The file \"$viewPath\" was created failed.\n";
                }
    
            }else{
                $body.= 'JsonModel();';
            }
    
            $classGenerator->addMethod(
                $actionName.'Action',
                [],
                MethodGenerator::FLAG_PUBLIC,
                $body
                );
    
            $fileGenerator->setClass($classGenerator);
            if(file_put_contents($filename, $fileGenerator->generate()))
            {
                echo "[Build success:] The action \"$actionName\" was created successfully.\n";
            }else{
                echo "[Build failed:] The action \"$actionName\" was created failed.\n";
            }
        }else{
            echo "The action \"$actionName\" already exists.\n";
        }
    }    
}