<?php
namespace Pinwin\View\Helper;

//use Cbschuld\Browser;
use Interop\Container\ContainerInterface;
use Zend\View\Helper\HeadScript;


class FootScript extends \Zend\View\Helper\HeadScript
{
    protected $regKey = 'Pinwin_View_Helper_FootScript';
    
    /**
     *
     * @var ContainerInterface
     */
    protected $services;
    
    public function __construct(ContainerInterface $services)
    {
        $this->services = $services;
        $this->setSeparator(PHP_EOL);
    }
    
    public function appendDojo($src, $extraOptions=[])
    {
        $browser = new \Browser();
        $config = $this->services->get('config')['dojo'];        
        if(isset($config['jquery']))
        {            
            if(!isset($config['packages']))
            {
                $config['packages'] = [];
            }
                
            $jquery_packages = [];
            
            if(strtolower($browser->getBrowser()) === 'internet explorer'
                && floatval($browser->getVersion()) < 10)
            {
                $jquery_packages = $config['jquery']['slow'];
            }else{
                $jquery_packages = $config['jquery']['fast'];
            }
            
            
            foreach ($jquery_packages as $package)
            {
                $config['packages'][] = $package;
                if($config['jquery']['deps'] === true)
                {
                    if(!isset($config['deps']))
                    {
                        $config['deps'] = array();
                    }    
                    $config['deps'][] = $package['name'];
                }                    
            }
            unset($config['jquery']);
        }
        
        $config['baseUrl'] = $this->view->basePath().'/';
        
        $config = json_encode($config);
        $config = preg_replace('/^\{/', '', $config);
        $config = preg_replace('/\}$/', '', $config);   
        
        $this->setAllowArbitraryAttributes(true);
        $this->setFile(
            $src, 
            'text/javascript', 
            ['data-dojo-config'=>$config]
        );
        
        //$this->appendScript('define.amd.jQuery = true;');
        
        return $this;
    }    
    
}