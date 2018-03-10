<?php

/**
 *
 * @author web
 *
 */
namespace baft\org\service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use baft\org\application;
use baft\org\applicationEvent;
use baft\org\organizationManager;
use \baft\org\listener\organizationResolveListener;
use baft\org\listener\autoloadListener;

class organizationFactory implements FactoryInterface {

	/**
	 * Creates and returns the organization
	 *
	 * @param ServiceLocatorInterface $serviceLocator        	
	 * @return application
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {

		$organization = new organizationManager ( $serviceLocator->get ( 'organizationsConfig' ), $serviceLocator );
		
		$organization->setEventManager ( $serviceLocator->get ( 'EventManager' ) );
		
		$organization->getEventManager ()->attach ( applicationEvent::EVENT_LOAD_ORGANIZATION_RESOLVE, new organizationResolveListener ( $organization->getEvent () ) );
		
		$organization->getEventManager ()->attach ( applicationEvent::EVENT_LOAD_ORGANIZATION, new autoloadListener ( $organization->getEvent () ) );
		
		// we need serviceManager and other stuff so use organazation object not other listener
		$organization->getEventManager ()->attach ( applicationEvent::EVENT_LOAD_ORGANIZATION, [ 
				$organization,
				'onLoadOrganization' 
		] );
		
		return $organization;
	
	}


}
