<?php

namespace baft\mvc;

use \Zend\Mvc\ApplicationInterface;
use baft\org\organization;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use baft\org\service\organizationFactory;

class applicationDecorator implements ApplicationInterface {
	
	/**
	 *
	 * @var \Zend\Mvc\Application
	 */
	protected $application;
	protected $organization;

	public function __construct($application, $organization) {

		$this->application = $application;
		$this->organization = $organization;
	
	}

	/**
	 *
	 * @param
	 *        	array | \Zend\Config\Config $config
	 * @throws \Exception
	 * @return boolean|\baft\mvc\applicationDecorator
	 */
	public static function init($applicationName = 'baft', $environmentName = 'production', $config) {

		/*
		 *
		 * $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
		 * $serviceManager = new ServiceManager(new Service\ServiceManagerConfig($smConfig));
		 * $serviceManager->setService('ApplicationConfig', $configuration);
		 * $serviceManager->get('ModuleManager')->loadModules();
		 *
		 * $listenersFromAppConfig = isset($configuration['listeners']) ? $configuration['listeners'] : array();
		 * $config = $serviceManager->get('Config');
		 * $listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : array();
		 *
		 * $listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));
		 *
		 * return $serviceManager->get('Application')->bootstrap($listeners);
		 *
		 */
		if (is_array ( $config ))
			$config = new \Zend\Config\Config ( $config );
		
		if (! $config instanceof \Zend\Config\Config) {
			throw new \Exception ( 'application initilization expect passed configuration to be array or instance of `\Zend\Config\Config` ' );
			return false;
		}
		
		$SMConfig = $config->get ( "service_manager", array () );
		$SMConfig ['factories'] ['organization'] = 'baft\org\service\organizationFactory';
		
		$mvcSmConfig = new ServiceManagerConfig ( $SMConfig );
		$serviceManager = new ServiceManager ( $mvcSmConfig );
		
		$serviceManager->setService ( 'ApplicationConfig', $config->get ( 'application', array () ) );
		$serviceManager->setService ( 'organizationsConfig', $config->get ( 'organization', array () ) );
		
		$organization = $serviceManager->get ( 'organization' );
		$organization->loadApplication ( APPLICATION );
		
		$serviceManager->get ( 'ModuleManager' )->loadModules ();
		
		$application = $serviceManager->get ( 'Application' );
		
		return new static ( $application, $organization );
	
	}

	public function bootstrap() {

		$listenersFromAppConfig = $this->getServiceManager ()->get ( 'ApplicationConfig' )->get ( "listeners", array () );
		$config = $this->getServiceManager ()->get ( 'Config' );
		$listenersFromConfigService = isset ( $config ['listeners'] ) ? $config ['listeners'] : array ();
		$listeners = array_unique ( array_merge ( $listenersFromConfigService, $listenersFromAppConfig ) );
		
		return $this->application->bootstrap ( $listeners );
	
	}

	public function getEventManager() {

		return parent::getEventManager ();
	
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\ApplicationInterface::getServiceManager()
	 */
	public function getServiceManager() {

		return $this->application->getServiceManager ();
	
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\ApplicationInterface::getRequest()
	 */
	public function getRequest() {

		return $this->application->getRequest ();
	
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\ApplicationInterface::getResponse()
	 */
	public function getResponse() {

		return $this->application->getResponse ();
	
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\ApplicationInterface::run()
	 */
	public function run() {

		return $this->application->run ();
	
	}


}