<?php
namespace Pinwin\Events\Listener;

use Zend\Mvc\MvcEvent;

class FinishAggregate extends AbstractAggregate{
    
    public function TemplateListener(MvcEvent $e)
    {
        
        if(!$this->isModuleUse($e)){
            return ;
        }
        if($e->getRequest()->isXmlHttpRequest())
        {
            return ;
        }
        
        
        if(APP_ENV != 'production')
        {
            return ;
        }
        
        $response = $e->getResponse();
        $content = $response->getBody();
        $content = $this->sanitize_output($content);
        $response->setContent($content);
    }
    
    protected function sanitize_output($buffer) {
    
        $search = array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/<!--(.|\s)*?-->/'
        );
    
        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );
    
        $buffer = preg_replace($search, $replace, $buffer);
    
        return $buffer;
    }
    
}