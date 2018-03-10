<?php

namespace baft\org;

use baft\std\flattenTree\flattenTree;
use Zend\Config\Config as zendConfig;
use baft\std\flattenTree\nodeInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\Config\Config;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class organizationManager implements EventManagerAwareInterface {
	
	/**
	 *
	 * @var \baft\std\flattenTree\flattenTree
	 */
	private $organizations;
	
	/**
	 *
	 * @var \Zend\EventManager\EventManager
	 */
	private $eventManager;
	
	/**
	 *
	 * @var \baft\org\applicationEvent
	 */
	private $event;
	
	/**
	 *
	 * @var \Zend\ServiceManager\ServiceLocatorInterface
	 */
	private $serviceLocator;
	
	/**
	 *
	 * @var \baft\org\applicationEvent
	 */
	private $applicationEvent;
	const APPLICATION_SCOPE = 'organization';

	/*
	 * (non-PHPdoc) @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
	 */
	public function getServiceLocator() {

		return $this->serviceLocator;
	
	}

	/*
	 * (non-PHPdoc) @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
	 */
	public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {

		$this->serviceLocator = $serviceLocator;
	
	}

	/**
	 *
	 * @param
	 *        	array | Zend\Config\Config | baft\std\flattenTree\flattenTree $applicationsTree
	 *        	array (
	 *        	"root" => "rootPathOfApplications",
	 *        	"default" => "AppName", // default app to load in any case
	 *        	"applications" => array (
	 *        	//config is as flattenTree::fromArray input array
	 *        	"AppName" => array (
	 *        	'instance' => nodeInstanceObject | "AppName", <-- is optional , default is instance of baft\org\application with AppName
	 *        	'parent' => "parent AppName",
	 *        	'children' => array ( 'childAppName' ),
	 *        	'property' => array ( applicationConfig | pathOfAppConfig )
	 *        	), . . .
	 *        	)
	 *        	)
	 * @throws \Exception
	 */
	public function __construct($applicationsTree, $serviceLocator) {
		
		// if $applicationsTree is zend config
		if ($applicationsTree instanceof zendConfig)
			$applicationsTree = $applicationsTree->toArray ();
		
		if (is_array ( $applicationsTree )) {
			if (! isset ( $applicationsTree ['applications'] ) || empty ( $applicationsTree ['applications'] ))
				throw new \Exception ( "no application defined for organization" );
			
			if (! isset ( $applicationsTree ['default_applicatin'] ))
				throw new \Exception ( 'organization expect to "default_application" property set in configuration' );
			
			$applications = &$applicationsTree ['applications'];
			foreach ( $applications as $applicationName => &$applicationConf ) {
				
				if (! isset ( $applicationConf ['instance'] ))
					$applicationConf ['instance'] = $applicationName;
				
				if (! $applicationConf ['instance'] instanceof nodeInterface)
					$applicationConf ['instance'] = new organization ( $applicationConf ['instance'] );
			}
			
			$this->organizations = flattenTree::fromArray ( $applicationsTree ['applications'], self::APPLICATION_SCOPE );
		}
		
		$this->setDefaultApplication ( $applicationsTree ['default_applicatin'] );
		
		if ($applicationsTree instanceof flattenTree)
			$this->organizations = $applicationsTree;
		
		$this->applicationEvent = new applicationEvent ();
		$this->setServiceLocator ( $serviceLocator );
	
	}

	/**
	 *
	 * @param unknown $appName        	
	 * @throws \Exception
	 * @return baft\org\application
	 */
	public function loadApplication($appName) {

		if ($appName === false) {
			// set default application name
			$appName = $this->getDefaultApplication ();
		}
		
		$organizationBranch = $this->organizations->getNodeParents ( $appName, self::APPLICATION_SCOPE );
		
		$this->getEvent ()->setOrganizationBranch ( $organizationBranch );
		
		$applicationConf = [ ];
		$organizationNode = $organizationBranch->shift ();
		while ( $organizationNode ) {
			
			$orgConfig = $organizationNode ['property'];
			$orgName = $organizationNode ['instance']->getName ();
			
			$this->getEvent ()->setParam ( 'orgConfig', $orgConfig )->setParam ( 'orgName', $orgName );
			
			$this->getEventManager ()->trigger ( applicationEvent::EVENT_LOAD_ORGANIZATION_RESOLVE, $this, $this->getEvent () );
			
			$this->getEventManager ()->trigger ( applicationEvent::EVENT_LOAD_ORGANIZATION, $this, $this->getEvent () );
			
			$organization = $this->getEvent ()->getOrganization ();
			
			$organizationNode ['instance']->setInstance ( $organization );
			
			if (! $organization)
				throw new \Exception ( "can not load organization .  '$orgName' organization dose not found." );
			
			try {
				$organizationNode = $organizationBranch->shift ();
			}
			catch ( \Exception $ex ) {
				$organizationNode = false;
			}
		}
		
		$applicationConf = $this->getServiceLocator ()->get ( 'organizationsConfig' )->merge ( $this->getServiceLocator ()->get ( 'ApplicationConfig' ) )->toArray ();
		
		$globConfPaths = array_reverse ( $applicationConf ['module_listener_options'] ['config_glob_paths'] );
		
		$applicationConf ['module_listener_options'] ['config_glob_paths'] = $globConfPaths;
		
		$this->getServiceLocator ()->setAllowOverride ( true )->setService ( 'ApplicationConfig', $applicationConf )->setAllowOverride ( false );
	
	}

	public function onLoadOrganization(applicationEvent $e) {

		$organization = $e->getOrganization ();
		
		if (! $organization instanceof ConfigProviderInterface && ! method_exists ( $organization, 'getConfig' )) {
			return;
		}
		
		// @TODO resolve to array or throw exception if 'getConfig' return no array
		$orgConfig = new Config ( $organization->getConfig () );
		
		$this->getServiceLocator ()->get ( 'organizationsConfig' )->merge ( $orgConfig );
	
	}

	/**
	 * Listen to the "route" event and attempt to route the request
	 *
	 * If no matches are returned, triggers "dispatch.error" in order to
	 * create a 404 response.
	 *
	 * Seeds the event with the route match on completion.
	 *
	 * @param MvcEvent $e        	
	 * @return null Router\RouteMatch
	 */
	public function onRoute(EventInterface $e) {

		$target = $e->getTarget ();
		$request = $e->getRequest ();
		$router = $e->getRouter ();
		$routeMatch = $router->match ( $request );
		
		if (! $routeMatch instanceof Router\RouteMatch) {
			$e->setError ( Application::ERROR_ROUTER_NO_MATCH );
			
			$results = $target->getEventManager ()->trigger ( MvcEvent::EVENT_DISPATCH_ERROR, $e );
			if (count ( $results )) {
				$return = $results->last ();
			} else {
				$return = $e->getParams ();
			}
			return $return;
		}
		
		$e->setRouteMatch ( $routeMatch );
		return $routeMatch;
	
	}

	/**
	 * get default application when no application specified form server
	 */
	public function getDefaultApplication() {

		return $this->defaultApplication;
	
	}

	public function setDefaultApplication($appName) {

		if (! $this->organizations->findNode ( $appName, self::APPLICATION_SCOPE ))
			throw new \Exception ( "default application name '$appName' not found in organiaztion config ." );
		$this->defaultApplication = $appName;
	
	}

	/*
	 * (non-PHPdoc) @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
	 */
	public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {

		$eventManager->setIdentifiers ( array (
				__CLASS__,
				get_class ( $this ),
				'organization' 
		) );
		$this->eventManager = $eventManager;
	
	}

	/*
	 * (non-PHPdoc) @see \Zend\EventManager\EventsCapableInterface::getEventManager()
	 */
	public function getEventManager() {

		if (! $this->eventManager instanceof EventManagerInterface) {
			$this->setEventManager ( new EventManager () );
		}
		return $this->eventManager;
	
	}

	public function getEvent() {

		if (! isset ( $this->event ))
			$this->setEvent ( new applicationEvent () );
		return $this->event;
	
	}

	public function setEvent($event) {

		$this->event = $event;
	
	}


}
