<?php
namespace Pinwin\Extensions\User;

use Pinwin\Extensions\ExtensionAggregate;
// use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use PinwinORM\GatewayFactory;

class UserAggregate extends ExtensionAggregate
{

    public function AuthRoute(MvcEvent $e)
    {
        if (! $this->isModuleUse($e))
            return;
        
        $service = $e->getApplication()->getServiceManager();
        $sessionContainerIndex = $this->moduleNamespace . 'Session';
        $sessionContainerIndex = lcfirst($sessionContainerIndex);
        $sessionContainer = $service->get($sessionContainerIndex);
        
        $service = $e->getTarget()->getServiceManager();
        
        $this->initSessionExtends($sessionContainer);
        
        // var_export( $sessionContainer->extends['user'] );
        if (! isset($sessionContainer->extends['user'])) {
            $sessionContainer->extends['user'] = [];
            
            $adapter = $service->get(MAIN_DB_ADAPTER);
            $installerTableGateway = GatewayFactory::Factory(EXTENSIONS_INSTALLATION_TABLE, $adapter);
            
            $sessionContainer->extends['user']['configRowSet'] = $installerTableGateway->select([
                'module' => $this->moduleNamespace,
                'is_enable' => 1
            ])->current()->toArray();
            
            $sessionContainer->extends['user']['config'] = empty($sessionContainer->extends['user']['configRowSet']) ? null : json_decode($sessionContainer->extends['user']['configRowSet']['config'], true);
        }
        
        if (! $sessionContainer->extends['user']['config'])
            return;
        
        $config = $sessionContainer->extends['user']['config'];
        $controller = $config['login']['controller'];
        $action = $config['login']['action'];
        
        $routeController = $e->getRouteMatch()->getParam('controller');
        $routeAction = $e->getRouteMatch()->getParam('action');
        
        if ((! $sessionContainer->user)) {
            
            if (($controller != $routeController) || ($action != $routeAction)) {
                $redirectUri = '';
                $redirectUri = isset($config['login']['segment']) ? $config['login']['segment'] : '';
                if (isset($config['login']['controller'])) {
                    if (strlen($redirectUri) > 0) {
                        $redirectUri .= '/';
                    }
                    $redirectUri .= $controller;
                }
                if (isset($config['login']['action'])) {
                    if (strlen($redirectUri) > 0) {
                        $redirectUri .= '/';
                    }
                    $redirectUri .= $action;
                }
                header('Location:' . $redirectUri);
                exit();
            }
        } else {
            
        }
    }
}

