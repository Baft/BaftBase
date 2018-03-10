<?php

namespace baft\org;

use baft\std\flattenTree\nodeInterface;
use baft\mvc\coreAbstract;

class organization implements nodeInterface {
	private $name;

	public function __construct($name) {

		$this->name = $name;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
	 */
	public function getEventManager() {
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc)
	 * @see \baft\std\flattenTree\nodeInterface::getName()
	 */
	public function getName() {

		return $this->name;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \baft\std\flattenTree\nodeInterface::setInstance()
	 */
	public function setInstance($instance) {
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc)
	 * @see \baft\std\flattenTree\nodeInterface::getInstance()
	 */
	public function getInstance() {
		// TODO Auto-generated method stub
	}


}