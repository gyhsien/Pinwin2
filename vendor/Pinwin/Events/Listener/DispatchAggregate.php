<?php
namespace Pinwin\Events\Listener;

use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Filter\Word\CamelCaseToDash;

class DispatchAggregate extends AbstractAggregate{
    
    public function TemplateListener(MvcEvent $e)
    {
        if(!$this->isModuleUse($e)){
            return ;
        }
        
        //找Action
        $routeParams = $e->getRouteMatch()->getParams();
        if($routeParams['action'] === 'index')
        {
            $template = preg_replace(
                '/Controller/', 
                '', 
                $routeParams['controller']);
            
            //在某些情形下$routeParams['controller']並不會列出完整的
            //Controller class name
            //只會列出Controller 的簡稱 ex.ContentController is content
            //用以下解法可以相容這兩種情形
            $template = preg_replace('/\\\\+/', '/', $template);
            $template = $routeParams['__NAMESPACE__'].'/'.str_replace(
                $routeParams['__NAMESPACE__'].'/', '', $template);
            
            
            $template = explode('/', $template);
            $worlfilter = new CamelCaseToDash();
            foreach ($template as &$word)
            {
                $word = strtolower($worlfilter->filter($word));
            }
            $template = implode('/', $template);
            if($e->getViewModel()->hasChildren())
            {
                $childrens = $e->getViewModel()->getChildren();
                foreach ($childrens as $viewModel)
                {
                    if(false !== preg_match(
                        '/index$/', $viewModel->getTemplate())
                    )
                    {
                        $viewModel->setTemplate($template);
                    }
                }
                
            }else{
                $e->getViewModel()->setTemplate($template);
            }
            return ;
        }
        return ;
    }
    
    public function LayoutListener(EventInterface $e)
    {
        
        if(!$this->isModuleUse($e)){
            return ;
        }
        
        $layout_index = $this->moduleNamespace.'/'.'layout';
        $target = $e->getTarget();
        if($e->getTarget() instanceof  \Zend\Stdlib\DispatchableInterface)
        {
            if(!$target->layout()->terminate())
            {
                $target->layout($layout_index);
            }
    
        }
    }
    
}