<?php

namespace BaftBase\std\flattenTree;

/**
 * each node of tree .
 *
 *
 * use this class to store instance objects in tree
 * when instances dose not implemented nodeInterface
 *
 * @author web
 *        
 */
class node implements nodeInterface {
	private $name;
	private $instance;

	public function __construct($name) {

		$this->name = $name;
	
	}

	public function getName() {

		return $this->name;
	
	}

	public function setInstance($instance) {

		$this->instance = $instance;
		return $this;
	
	}

	public function getInstance() {

		return $this->instance;
	
	}


}