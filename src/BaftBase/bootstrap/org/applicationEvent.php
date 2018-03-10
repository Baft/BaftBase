<?php

namespace baft\org;

use Zend\EventManager\Event;
use Zend\Stdlib\SplStack;

class applicationEvent extends Event {
	
	/**
	 *
	 * @var \Zend\Stdlib\CallbackHandler[]
	 */
	protected $listeners = array ();
	
	/**
	 * application events triggered by eventmanager
	 */
	const EVENT_LOAD_ORGANIZATION_RESOLVE = 'loadOrganization.resolve';
	const EVENT_LOAD_ORGANIZATION = 'loadOrganization';
	const EVENT_ORGANIZATION_CONFIG = 'loadOrganization.mergeConfig';
	const EVENT_LOAD_ORGANIZATION_POST = 'loadOrganization.post';
	
	/**
	 * parents branch of current organization
	 *
	 * @var \Zend\Stdlib\SplStack
	 */
	protected $organizationBranch;
	
	/**
	 *
	 * @var baft\org\application
	 */
	protected $organization = false;
	
	/**
	 *
	 * @var Listener\ConfigMergerInterface
	 */
	protected $configListener;

	/**
	 * Get the application
	 *
	 * @return string
	 */
	public function getOrganization() {

		return $this->organization;
	
	}

	/**
	 * Set the application
	 *
	 * @param baft\org\application $organization        	
	 * @throws Exception\InvalidArgumentException
	 * @return applicationEvent
	 */
	public function setOrganization($organization) {

		$this->organization = $organization;
		
		return $this;
	
	}

	public function setOrganizationBranch($organizationBranch) {

		$this->organizationBranch = $organizationBranch;
	
	}

	public function getOrganizationBranch() {

		return $this->organizationBranch;
	
	}


}