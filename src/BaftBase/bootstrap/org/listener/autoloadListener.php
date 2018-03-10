<?php

namespace baft\org\listener;

use Zend\Loader\AutoloaderFactory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use baft\org\applicationEvent;

/**
 * Autoloader listener
 * just like module
 */
class autoloadListener {

	/**
	 *
	 * @param applicationEvent $e        	
	 * @return void
	 */
	public function __invoke(applicationEvent $e) {

		$organization = $e->getOrganization ();
		if (! $organization instanceof AutoloaderProviderInterface && ! method_exists ( $organization, 'getAutoloaderConfig' )) {
			return;
		}
		
		$autoloaderConfig = $organization->getAutoloaderConfig ();
		AutoloaderFactory::factory ( $autoloaderConfig );
	
	}


}
