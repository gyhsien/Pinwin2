<?php
namespace Webservice\Controller;

use Pinwin\Mvc\Controller\InjectServiceController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Gregwar\Captcha\CaptchaBuilder;

class CaptchaController extends InjectServiceController
{
    
    public function indexAction()
    {
        $headers = $this->response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'image/jpeg');
        $this->response->setHeaders($headers);
        $captcha = new CaptchaBuilder();
        $phrase = $captcha->getPhrase();
        $container = $this->container->get('webserviceSession');
        $container->captcha = $phrase;
        
        $viewModel = new ViewModel(
            ['captcha'=>$captcha->create($phrase)->build(150, 32)]
        );
        $viewModel->setTerminal(true);
        return $viewModel;
        
    }
    
}
