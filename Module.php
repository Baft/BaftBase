<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the
 *       canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc.
 *            (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
namespace BaftBase;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Session as session;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Loader\StandardAutoloader;
use Zend\ModuleManager\ModuleEvent;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;
use Zend\ServiceManager\ServiceManager;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface, BootstrapListenerInterface
{

    public function onBootstrap(EventInterface $e)
    {
        $em = $e->getTarget()->getEventManager();
        $sm = $e->getTarget()->getServiceManager();
        
        $em->attach(new ModuleRouteListener());
    }
    
    /*
     * (non-PHPdoc)
     * @see \Zend\ModuleManager\Feature\ServiceProviderInterface::getServiceConfig()
     */
    public function getServiceConfig()
    {
        $serviceConfig = include_once (__DIR__ . '/config/service.config.php');
        
        return $serviceConfig;
    }

    public function getRouteConfig()
    {
        return [
            'invokable' => []        
        ];
    }

    public function getFormElementConfig()
    {
        $FormConfig = include_once (__DIR__ . '/config/form.config.php');
        return $FormConfig;
    }

    public function getConfig()
    {
        $moduleConfig = include_once (__DIR__ . '/config/module.config.php');
        $viewConfig = include_once (__DIR__ . '/config/view.config.php');
        $config = array_merge_recursive($moduleConfig, $viewConfig);
        return $config;
    }

    public function getAutoloaderConfig()
    {
        $moduleNamespaces = include_once (__DIR__ . '/config/namespaces.config.php');
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . DS .'autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                StandardAutoloader::LOAD_NS => $moduleNamespaces
            )
        );
    }
}
