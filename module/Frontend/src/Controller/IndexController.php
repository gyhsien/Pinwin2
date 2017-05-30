<?php
namespace Frontend\Controller;

use Zend\View\Model;
use Pinwin\Mvc\Controller\InjectServiceController;

class IndexController extends InjectServiceController
{

    protected $sub_set = [];
    
    public function indexAction()
    {        
		$adapter = $this->container->get('masterDbAdapter');
		$sql = new \Zend\Db\Sql\Sql($adapter);
// 		$select = new \Zend\Db\Sql\Select();
		$select = $sql->select('grs_admin');
		$sql->prepareStatementForSqlObject($select)->execute();
		return new Model\ViewModel();
    }

    
    public function loginAction()
    {
        return new Model\ViewModel();
    }
}
