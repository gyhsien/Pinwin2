<?php
//php vendor/bin/pinwin/schema.php
//php vendor/bin/pinwin/schema.php --config=Pinwin2Adapter
/**
 *php vendor/bin/pinwin/schema.php --driver=pdo_mysql --database=pimcore --username=root  --hostname=127.0.0.1 --password=1234
 * --driver=pdo_mysql --database=pimcore --username=root  --hostname=127.0.0.1 --password=1234
 */
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Metadata\Source\Factory as MetadataFactory;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;
//use Zend\Db\Metadata\Object\TableObject;

if(!defined('MAIN_DB_ADAPTER'))
{
    require 'config/constants.php';
}

$start = time();

$basePath = dirname(dirname(dirname(__DIR__)));
ini_set('error_log', 'vendor/bin/pinwin/php_errors_'.date("Ymd").'.log');
require 'vendor/autoload.php';
// require 'vendor/Pinwin/Tools/Tools.php';

try {
    
    
    if(count($argv) == 1)
    {
        $sysConfig = array_merge_recursive(
            require 'config/autoload/global.php', 
            require 'config/autoload/local.php'
        );
        
        $config = $sysConfig['db']['adapters'][MAIN_DB_ADAPTER];
        
    }else{
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
    }
    
    
    
    $dbAdapter = new DbAdapter($config);
    
    $metadata = MetadataFactory::createSourceFromAdapter($dbAdapter);
    
    $current_schema = $dbAdapter->getCurrentSchema();
  
    $tables = $metadata->getTableNames();
    $wordFilter = new \Zend\Filter\Word\SeparatorToCamelCase();
    //'Entity'
    $logContent = '';
    foreach ($tables as $table)
    {
        $tableObject = $metadata->getTable($table, $current_schema);        
        
        $wordFilter->setSeparator('-');
        $baseClassName = $wordFilter->filter($table);
        $wordFilter->setSeparator('_');
        $baseClassName = $wordFilter->filter($baseClassName);
        $tableClassName = $baseClassName.'Table';
        $entityClassName = $baseClassName.'Entity';
        
        $wordFilter->setSeparator('-');
        $namespaceSchema = $wordFilter->filter($current_schema);
        $wordFilter->setSeparator('_');
        $namespaceSchema = $wordFilter->filter($namespaceSchema);
        
        $namespace = 'PinwinORM\\'.$namespaceSchema;
        $baseNamespace = $namespace.'\\Base';
        
        $baseTableFileGenerator = new FileGenerator([
            'namespace' => $baseNamespace,
            'uses' => ['Pinwin\Db\TableGateway\AbstractTableGateway']
        ]);        
        $baseTableClassGenerator = new ClassGenerator();
        $baseTableClassGenerator->setAbstract(true);
        $baseTableClassGenerator->setExtendedClass('AbstractTableGateway');
        $baseTableClassGenerator->addConstant('TABLE', 'PREFIX_TABLE.\''.$table);
        $baseTableClassGenerator->setName('Base'.$tableClassName);
        $baseTableFileGenerator->setClass($baseTableClassGenerator);
        $abstractFilePath = str_replace("\\", '/', $baseNamespace);
        \Pinwin\Tools\Tools::mkdir_r('vendor/'.$abstractFilePath);
        
        $baseTableCode = $baseTableFileGenerator->generate();
        $baseTableCode = str_replace('\'PREFIX_TABLE.\\\'', 'PREFIX_TABLE.\'', $baseTableCode);
        $baseTableFilePath = 'vendor/'.$abstractFilePath.'/Base'.$tableClassName.'.php';
        
        if(is_file($baseTableFilePath))
        {
            echo "The file [".$baseTableFilePath."] already exists\n";
        }else{
            if(file_put_contents($baseTableFilePath, $baseTableCode))
            {
                $logContent.= "[Build success:] ".$baseTableFilePath.PHP_EOL;
                echo "[Build success:] ".$baseTableFilePath.PHP_EOL;
            }else{
                $logContent.= "[Build failed:] ".$baseTableFilePath.PHP_EOL;
                echo "[Build failed:] ".$baseTableFilePath.PHP_EOL;
            }            
        }
        
        $baseEntityFileGenerator = new FileGenerator([
            'namespace' => $baseNamespace,
            'uses' => ['Pinwin\Db\RowGateway\AbstractRowGateway']
        ]);
        $baseEntityClassGenerator = new ClassGenerator();
        $baseEntityClassGenerator->setAbstract(true);
        $baseEntityClassGenerator->setExtendedClass('AbstractRowGateway');
        $baseEntityClassGenerator->addProperty(
            'table', 
            'PREFIX_TABLE.\''.$table, 
            PropertyGenerator::FLAG_PROTECTED
        );
        
        //new TableObject($name);
        $primarykey = [];
        foreach($tableObject->getConstraints() as $constraint)
        {
            if($constraint->getType() == 'PRIMARY KEY')
            {
                $primarykey = $constraint->getColumns();
            }
        }
        
        $baseEntityClassGenerator->addProperty(
            'primaryKeyColumn',
            new PropertyValueGenerator(
                $primarykey, 
                PropertyValueGenerator::TYPE_ARRAY_SHORT,
                PropertyValueGenerator::OUTPUT_SINGLE_LINE
            ),
            PropertyGenerator::FLAG_PROTECTED
            );
        
        $baseEntityClassGenerator->setName('Base'.$entityClassName);
        $baseEntityFileGenerator->setClass($baseEntityClassGenerator);
        $abstractFilePath = str_replace("\\", '/', $baseNamespace);
        \Pinwin\Tools\Tools::mkdir_r('vendor/'.$abstractFilePath);
        
        $baseEntityCode = $baseEntityFileGenerator->generate();
        $baseEntityCode = str_replace('\'PREFIX_TABLE.\\\'', 'PREFIX_TABLE.\'', $baseEntityCode);
        $baseEntityFilePath = 'vendor/'.$abstractFilePath.'/Base'.$entityClassName.'.php';
        
        if(is_file($baseEntityFilePath))
        {
            echo "The file [".$baseEntityFilePath."] already exists\n";
        }else{
            if(file_put_contents($baseEntityFilePath, $baseEntityCode)
                ){
                    $logContent.= "[Build success:] ".$baseEntityFilePath.PHP_EOL;
                    echo "[Build success:] ".$baseEntityFilePath.PHP_EOL;
            }else{
                $logContent.= "[Build failed:] ".$baseEntityFilePath.PHP_EOL;
                echo "[Build failed:] ".$baseEntityFilePath.PHP_EOL;
            }            
        }
        
        $tableFileGenerator = new FileGenerator([
            'namespace' => $namespace,
            'uses' => [$baseNamespace.'\\Base'.$tableClassName]
        ]);
        
        $tableClassGenerator = new ClassGenerator();
        $tableClassGenerator->setExtendedClass('Base'.$tableClassName);
        $tableClassGenerator->setName($tableClassName);
        $tableFileGenerator->setClass($tableClassGenerator);
        $filePath = str_replace("\\", '/', $namespace);
        
        $tableClassFilePath = 'vendor/'.$filePath.'/'.$tableClassName.'.php';
        if(is_file($tableClassFilePath))
        {
            $logContent.= "The file [".$tableClassFilePath."] already exists\n";
            echo "The file [".$tableClassFilePath."] already exists\n";
        }else{
            if(file_put_contents($tableClassFilePath, $tableFileGenerator->generate()))
            {
                $logContent.= "[Build success:] ".$tableClassFilePath.PHP_EOL;
                echo "[Build success:] ".$tableClassFilePath.PHP_EOL;
            }else{
                $logContent.= "[Build failed:] ".$tableClassFilePath.PHP_EOL;
                echo "[Build failed:] ".$tableClassFilePath.PHP_EOL;
            }
            
        }
        
        $entityFileGenerator = new FileGenerator([
            'namespace' => $namespace,
            'uses' => [$baseNamespace.'\\Base'.$entityClassName]
        ]);
        $entityClassGenerator = new ClassGenerator();
        //$entityClassGenerator->setAbstract(true);
        $entityClassGenerator->setExtendedClass('Base'.$entityClassName);
        $entityClassGenerator->setName($entityClassName);
        $entityFileGenerator->setClass($entityClassGenerator);
        $filePath = str_replace("\\", '/', $namespace);
        \Pinwin\Tools\Tools::mkdir_r('vendor/'.$filePath);
        
        $entityClassFilePath = 'vendor/'.$filePath.'/'.$entityClassName.'.php';
        if(is_file($entityClassFilePath))
        {
            $logContent.= "The file [".$entityClassFilePath."] already exists\n";
            echo "The file [".$entityClassFilePath."] already exists\n";
        }else{
            if(file_put_contents($entityClassFilePath, $entityFileGenerator->generate())
                ){
                    $logContent.= "[Build success:] ".$entityClassFilePath.PHP_EOL;
                    echo "[Build success:] ".$entityClassFilePath.PHP_EOL;
            }else{
                $logContent.= "[Build failed:] ".$entityClassFilePath.PHP_EOL;
                echo "[Build failed:] ".$entityClassFilePath.PHP_EOL;
            }
            
        }
        
        echo "\n";
    }
    echo "Done.\n";
    $end = time();
    echo "Total spent : ". ($end-$start) ." seconds"; 
    file_put_contents('vendor/bin/pinwin/'.$config['database'].date("-Y-m-d").'.log', $logContent);
} catch (Exception $e) {
    var_export($e);
//     echo $e->getMessage();
}

