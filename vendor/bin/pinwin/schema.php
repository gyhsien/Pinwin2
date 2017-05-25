<?php
//php vendor/pinwin/bin/schema.php --config=Pinwin2Adapter
/**
 *php vendor/pinwin/bin/schema.php --driver=pdo_mysql --database=pimcore --username=root  --hostname=127.0.0.1 --password=1234
 * --driver=pdo_mysql --database=pimcore --username=root  --hostname=127.0.0.1 --password=1234
 */
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Metadata\Source\Factory as MetadataFactory;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;
use Zend\Db\Metadata\Object\TableObject;

$start = time();

$basePath = dirname(dirname(dirname(__DIR__)));
ini_set('error_log', 'vendor/pinwin/bin/php_errors_'.date("Ymd").'.log');
require 'vendor/autoload.php';
require 'vendor/Pinwin/Tools/Tools.php';

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
        $baseTableClassGenerator->addConstant('TABLE', $table);
        $baseTableClassGenerator->setName('Base'.$tableClassName);
        $baseTableFileGenerator->setClass($baseTableClassGenerator);
        $abstractFilePath = str_replace("\\", '/', $baseNamespace);
        \Pinwin\Tools\Tools::mkdir_r('vendor/'.$abstractFilePath);
        
        if(file_put_contents('vendor/'.$abstractFilePath.'/Base'.$tableClassName.'.php', $baseTableFileGenerator->generate()))
        {
            $logContent.= "[Build success:] vendor/".$abstractFilePath.'/Base'.$tableClassName.".php\n";
            echo "[Build success:] vendor/".$abstractFilePath.'/Base'.$tableClassName.".php\n";
        }else{
            $logContent.= "[Build failed:] vendor/".$abstractFilePath.'/Base'.$tableClassName.".php\n";
            echo "[Build failed:] vendor/".$abstractFilePath.'/Base'.$tableClassName.".php\n";
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
            $table, 
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
        if(file_put_contents(
            'vendor/'.$abstractFilePath.'/Base'.$entityClassName.'.php', 
            $baseEntityFileGenerator->generate()
            )
        ){
            $logContent.= "[Build success:] vendor/".$abstractFilePath.'/Base'.$entityClassName.".php\n";
            echo "[Build success:] vendor/".$abstractFilePath.'/Base'.$entityClassName.".php\n";
        }else{
            $logContent.= "[Build failed:] vendor/".$abstractFilePath.'/Base'.$entityClassName.".php\n";
            echo "[Build failed:] vendor/".$abstractFilePath.'/Base'.$entityClassName.".php\n";
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
        if(!is_file('vendor/'.$filePath.'/'.$tableClassName.'.php'))
        {
            if(file_put_contents('vendor/'.$filePath.'/'.$tableClassName.'.php', $tableFileGenerator->generate()))
            {
                $logContent.= "[Build success:] vendor/".$filePath.'/'.$tableClassName.".php\n";
                echo "[Build success:] vendor/".$filePath.'/'.$tableClassName.".php\n";
            }else{
                $logContent.= "[Build failed:] vendor/".$filePath.'/'.$tableClassName.".php\n";
                echo "[Build failed:] vendor/".$filePath.'/'.$tableClassName.".php\n";
            }
        }else{
            $logContent.= "The file [vendor/".$filePath.'/'.$tableClassName.".php] already exists\n";
            echo "The file [vendor/".$filePath.'/'.$tableClassName.".php] already exists\n";
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
        if(!is_file('vendor/'.$filePath.'/'.$entityClassName.'.php'))
        {
            if(file_put_contents(
                'vendor/'.$filePath.'/'.$entityClassName.'.php',
                $entityFileGenerator->generate()
                )
                ){
                    $logContent.= "[Build success:] vendor/".$filePath.'/'.$entityClassName.".php\n";
                    echo "[Build success:] vendor/".$filePath.'/'.$entityClassName.".php\n";
            }else{
                $logContent.= "[Build failed:] vendor/".$filePath.'/'.$entityClassName.".php\n";
                echo "[Build failed:] vendor/".$filePath.'/'.$entityClassName.".php\n";
            }            
        }else{
            $logContent.= "The file [vendor/".$filePath.'/'.$entityClassName.".php] already exists\n";
            echo "The file [vendor/".$filePath.'/'.$entityClassName.".php] already exists\n";
        }
        
        echo "\n";
    }
    echo "Done.\n";
    $end = time();
    echo "Total spent : ". ($end-$start) ." seconds"; 
    file_put_contents('vendor/pinwin/bin/'.$config['database'].date("-Y-m-d").'.log', $logContent);
} catch (Exception $e) {
    echo $e->getMessage();
}

