<?php
namespace Pinwin\Db\TableGateway;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Adapter\Adapter;
//use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbTableGateway as PaginatorDbTableAdapter;
use Zend\Paginator\Adapter\DbSelect as PaginatorDbSelectAdapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\Feature\EventFeature;

class AbstractTableGateway extends TableGateway{
    
    public function __construct(
        Adapter $adapter, 
        $features = null, 
        $resultSetPrototype = null, 
        Sql $sql = null)
    {
        $reflection = new \ReflectionObject($this);
        $this->table = new TableIdentifier($reflection->getConstant('TABLE'));
        
        parent::__construct(
            $this->table, $adapter, $features, $resultSetPrototype, $sql);
        //$evenFeature = new EventFeature();
        //$evenFeature->
        //$this->featureSet->addFeature();
    }
    
    
    /**
     * 
     * @param array $conditions
     * @param array $options
     * @return Paginator
     */
    public function paginatorTableWith($conditions = [], $options = [])
    {
        //$where = null, $order = null, $group = null, $having = null
        $conditions_keys = ['where', 'order', 'group', 'having'];
        foreach ($conditions_keys as $key)
        {
            if(empty($conditions[$key]))
            {
                $conditions[$key] = null;
            }
        }
        extract($conditions);
        $adapter = new PaginatorDbTableAdapter(
            $this, $where, $order, $group, $having);
        if(is_array($options))
        {
            if(count($options) > 0)
            {
                Paginator::setGlobalConfig($options);
            }
        }
        
        return new Paginator($adapter);
    }
    
    /**
     * 
     * @param Select $select
     * @param ResultSetInterface $resultSetPrototype
     * @return Paginator
     */
    public function paginatorSelectWith(
        Select $select, ResultSetInterface $resultSetPrototype = null
    )
    {
        $adapter = new PaginatorDbSelectAdapter(
            $select, $this->sql, $resultSetPrototype);
        
        return new Paginator($adapter);
    }    
}