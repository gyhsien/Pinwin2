<?php
namespace Pinwin\Mvc\Controller;
use Zend\Mvc\Controller\LazyControllerAbstractFactory as ZendLazyControllerAbstractFactory;
use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\Stdlib\DispatchableInterface;


class LazyControllerAbstractFactory extends ZendLazyControllerAbstractFactory
{
    
    /**
     * 
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return string
     */
    protected function requestNameToFullControllerClass($container, $requestedName)
    {

        if(class_exists($requestedName))
        {
            return $requestedName;
        }        
        
        $match = $container->get('router')->match($container->get('request'));
        $__NAMESPACE__ = $match->getParam('__NAMESPACE__');
        $controller = $match->getParam('controller');
        
        $filterWord = new \Zend\Filter\Word\SeparatorToCamelCase();
        $filterWord->setSeparator('-');
        $controllerClassName = $__NAMESPACE__ . '\\Controller\\'.$filterWord->filter($controller).'Controller';
        
        $requestedName = $controllerClassName;
        return $requestedName;
    }
    
    /**
     * override \Zend\Mvc\Controller\LazyControllerAbstractFactory::__invoke()
     * {@inheritDoc}
     *
     * @return DispatchableInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controllerFullName = $this->requestNameToFullControllerClass($container, $requestedName);
        
        
        $reflectionClass = new ReflectionClass($controllerFullName);
        
        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return new $controllerFullName();
        }
        
        $reflectionParameters = $constructor->getParameters();
        
        
        if (empty($reflectionParameters)) {
            return new $controllerFullName();
        }
        
        $parameters = array_map(
            $this->resolveParameter($container, $requestedName),
            $reflectionParameters
            );
        
        return new $controllerFullName(...$parameters);
        
        
    }
    
    
    /**
     * override \Zend\Mvc\Controller\LazyControllerAbstractFactory::canCreate()
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\LazyControllerAbstractFactory::canCreate()
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        
        return parent::canCreate($container, $this->requestNameToFullControllerClass($container, $requestedName));
    }
    
    /**
     * Resolve a parameter to a value.
     *
     * Returns a callback for resolving a parameter to a value.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return callable
     */
    private function resolveParameter(ContainerInterface $container, $requestedName)
    {
        /**
         * @param ReflectionClass $parameter
         * @return mixed
         * @throws ServiceNotFoundException If type-hinted parameter cannot be
         *   resolved to a service in the container.
         */
        return function ($parameter) use ($container, $requestedName) {
            if ($parameter->isArray()
                && $parameter->getName() === 'config'
                && $container->has('config')
                ) {
                    return $container->get('config');
                }
    
                if ($parameter->isArray()) {
                    return [];
                }
    
                if (! $parameter->getClass()) {
                    return;
                }
    
                $type = $parameter->getClass()->getName();
                $type = isset($this->aliases[$type]) ? $this->aliases[$type] : $type;
                if (! $container->has($type)) {
                    throw new ServiceNotFoundException(sprintf(
                        'Unable to create controller "%s"; unable to resolve parameter "%s" using type hint "%s"',
                        $requestedName,
                        $parameter->getName(),
                        $type
                        ));
                }
    
                return $container->get($type);
        };
    }    
    
}