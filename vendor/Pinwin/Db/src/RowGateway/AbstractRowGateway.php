<?php
namespace Pinwin\Db\RowGateway;

use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Adapter\Adapter;
//use Zend\Db\Metadata\Metadata;


class AbstractRowGateway extends RowGateway
{
    /**
     *
     * @var Adapter
     */
    protected $adapter;
    
    protected $readOnlyProperty = ['table', 'primaryKeyColumn'];
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $reflectionObject = new \ReflectionObject($this);
        parent::__construct($this->primaryKeyColumn, $this->table, $adapter);
        
        $metadata = \Zend\Db\Metadata\Source\Factory::createSourceFromAdapter($this->adapter);
        $tableObj = $metadata->getTable($this->table);        
        $tableObj->getConstraints();
    }
    
    
    public function __get($name)
    {
        
        if (array_search($name, $this->readOnlyProperty)) {
            throw new \Exception($name .' is read only.');
        }
        
        
        return parent::__get($name);
    }
    
}