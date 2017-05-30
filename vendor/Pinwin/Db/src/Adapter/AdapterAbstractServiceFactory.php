<?php
namespace Pinwin\Db\Adapter;

use Zend\Db\Adapter\AdapterAbstractServiceFactory as ZendAdapterAbstractServiceFactory;
use Zend\Db\Adapter\Adapter as ZendAdapter;
use BjyProfiler\Db\Adapter\ProfilingAdapter;
use Interop\Container\ContainerInterface;

class AdapterAbstractServiceFactory extends ZendAdapterAbstractServiceFactory 
{
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Db\Adapter\AdapterAbstractServiceFactory::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container);        
        $adapter = null;
        if(APP_ENV != 'production')
        {
            $adapter = new ProfilingAdapter($config[$requestedName]);
            if (php_sapi_name() == 'cli') {
                $logger = new \Zend\Log\Logger();
                // write queries profiling info to stdout in CLI mode
                $writer = new \Zend\Log\Writer\Stream('php://output');
                $logger->addWriter($writer, Zend\Log\Logger::DEBUG);
                $adapter->setProfiler(new \BjyProfiler\Db\Profiler\LoggingProfiler($logger));
            } else {
                $adapter->setProfiler(new \BjyProfiler\Db\Profiler\Profiler());
            }
            $adapter->injectProfilingStatementPrototype();
        }else{
            $adapter = new ZendAdapter($config[$requestedName]);
        }
        return $adapter;
    }
    
}