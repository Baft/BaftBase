<?php

/**
 *
 * @author web
 *
 */
namespace baft\org\listener;

use baft\org\applicationEvent;
use Zend\Loader\AutoloaderFactory;

/**
 * Module resolver listener
 */
class organizationResolveListener {

	/**
	 *
	 * @param applicationEvent $e        	
	 * @return object false if module class does not exist
	 */
	public function __invoke(applicationEvent $e) {

		$orgConfig = $e->getParam ( 'orgConfig' );
		$orgName = $e->getParam ( 'orgName' );
		if (! isset ( $orgConfig ['root'] ))
			throw new \Exception ( "can not load organization . root of organization '$orgName' dose not set in configuration." );
		
		AutoloaderFactory::factory ( [ 
				AutoloaderFactory::STANDARD_AUTOLOADER => [ 
						'namespaces' => [ 
								$orgName => $orgConfig ['root'] 
						] 
				] 
		] );
		$class = $orgName . '\organization';
		
		if (! class_exists ( $class )) {
			$e->setOrganization ( false );
			return false;
		}
		
		$organization = new $class ();
		$e->setOrganization ( $organization );
		return $organization;
	
	}


}
