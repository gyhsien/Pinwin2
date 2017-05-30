<?php
namespace Board\Controller;

use Pinwin\Mvc\Controller\InjectServiceController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class LoginController extends InjectServiceController
{


    
    public function indexAction()
    {
        $viewModel = new ViewModel();
        
        $viewModel->setTerminal(true);
        
        /*
        $adminUser = AdminUserQuery::create()->filterByAccount('admin')->findOne();
        
        if(strlen($adminUser->getPassword()) === 0)
        {
            if(APP_ENV == 'development')
            {
                $container = $this->container->get('boardSession');
                $container->token = Rand::getString(16);
                $viewModel->setVariable('token', $container->token);
                $viewModel->setTemplate('board/login/init');
            }
        }
        */
        
        return $viewModel;
    }
    
    public function initAdminPasswordAction()
    {
        if($this->request->isXmlHttpRequest() && $this->request->isPost())
        {
            
            $container = $this->container->get('boardSession');
            $token = $container->token;
            $model = new JsonModel();
            try {
                if($token == $_POST['token'])
                {
                    $status = false ;
                    /*
                    $adminUser = AdminUserQuery::create()->filterByAccount('admin')->findOne();
                    $crypt = new \Zend\Crypt\Password\Bcrypt();
                    $adminUser->setPassword($crypt->create($_POST['password']));
                    $adminUser->save();
                    $model->setVariables(array('status' => true));
                    */
                   //unset($container->token);
                }
                
            } catch (Exception $e) {
                $model->setVariables(array('status' => false, '_message'=>$e->getMessage()));
            }
            return $model;
        } 
        throw new \Exception('同學這樣不行喔');
        
    }
    
    public function loginAction()
    {                
        if($this->request->isXmlHttpRequest() && $this->request->isPost())
        {
            $container = $this->container->get('webserviceSession');
            $container->captcha;
            $model = new JsonModel();
            $post = $this->request->getPost();
            
            if( strtolower($post['captcha']) != strtolower($container->captcha) )
            {
                $model->setVariables(array(
                    'status'=>false, 
                    'message'=>'驗證碼錯誤'
                ));
                return $model;
            }
           
            
            
            return $model;
        }
        
        throw new \Exception('同學這樣不行喔');
    }
}
