<?php
namespace PinwinORM;
use Pinwin\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Filter\Word\SeparatorToCamelCase;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\Feature\FeatureSet;
use Zend\ServiceManager\ServiceManager;
class GatewayFactory
{
    
    
    /**
     * 
     * @var AbstractTableGateway[]
     */
    static private $tableGateways = [];
    
    /**
     * 
     * @var ServiceManager
     */
    //static private $container;
    
    /**
     * 
     * @param string $tableGateway
     * @param ServiceManager $container
     * @param FeatureSet[] $featureSets
     * @return AbstractTableGateway
     */
    static public function Factory(
        $tableGateway, 
        Adapter $adapter,
        //ServiceManager $container,
        $featureSets = null
        )
    {
        //self::$container = $container;
        $entityClassName = '';
        $tableGatewayClassName = $tableGateway;
        
        
        if(!class_exists($tableGatewayClassName))
        {
            $schemaClassname = $adapter->getCurrentSchema();
            
            $wordFilter = new SeparatorToCamelCase();
            $wordFilter->setSeparator('-');
            $schemaClassname = $wordFilter->filter(
                $schemaClassname);
            $tableGatewayClassName = $wordFilter->filter(
                $tableGatewayClassName);
            $wordFilter->setSeparator('_');
            $schemaClassname = $wordFilter->filter(
                $schemaClassname);
            $tableGatewayClassName = $wordFilter->filter(
                $tableGatewayClassName);
            $tableGatewayClassName.= 'Table';
            $tableGatewayClassName = 'PinwinORM\\'.
                $schemaClassname.'\\'.
                $tableGatewayClassName;            
        }

        $entityClassName = str_replace('Table', 'Entity', $tableGatewayClassName);
        $entityClassName = explode('\\', $entityClassName);
        $entityClassName = 'PinwinORM\\'
            .$schemaClassname.'\\'
            .end($entityClassName
        );
        
        if(empty(self::$tableGateways['']))
        {
            $tableGatewayObj = new $tableGatewayClassName(
                $adapter,
                new RowGatewayFeature(new $entityClassName($adapter))
                );
            if(is_array($featureSets))
            {
                if(count($featureSets) > 0)
                    $tableGatewayObj->getFeatureSet()->addFeatures($featureSets);
            }
            self::$tableGateways[$tableGatewayClassName] = $tableGatewayObj;
        }
        
        
        return self::$tableGateways[$tableGatewayClassName];
    }
}