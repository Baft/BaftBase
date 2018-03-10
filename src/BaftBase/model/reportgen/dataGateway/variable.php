<?php

namespace reportgen\dataGateway;

use Zend\Stdlib\Hydrator\HydratorOptionsInterface;

class variable implements HydratorOptionsInterface {
	public $name;
	public $type = '\Zend\InputFilter\Input';
	public $filters = array ();
	public $validations = array ();
	public $default;
	private $lock = false;
	private $data = null;
	private $properties = array ();

	public static function create($name, array $filters = array(), array $validations = array(), $default = null) {

		$variable = new static ();
		$variable->name = $name;
		$variable->default = $default;
		$variable->filters = $filters;
		$variable->validations = $validations;
		return $variable;
	
	}

	public function isLock() {

		return $this->lock;
	
	}

	/**
	 * lock the varible to avoid override
	 *
	 * @return \reportgen\dataGateway\variable
	 */
	public function setLock() {

		$this->lock = true;
		return $this;
	
	}

	/**
	 * free the variable for setting data
	 *
	 * @return \reportgen\dataGateway\variable
	 */
	public function setFree() {

		$this->lock = false;
		return $this;
	
	}

	public function setData($data) {

		if (! $this->lock)
			$this->data = $data;
		else
			throw new \Exception ( "varibale data of \"{$this->name}\" is set lately and is locked" );
		$this->lock = true;
		return $this;
	
	}

	public function getData() {

		return $this->data;
	
	}

	public function __isset($what) {

		if (isset ( $this->name ) && ! is_null ( $this->data ) && $this->lock)
			return true;
		return false;
	
	}

	public function toArray() {

		$array = array ();
		$array ['type'] = $this->type;
		$array ['name'] = $this->name;
		$array ['validators'] = $this->validations;
		$array ['filters'] = $this->filters;
		$array ['default'] = $this->default;
		$array = array_merge ( $array, $this->properties );
		return $array;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Stdlib\Hydrator\HydratorOptionsInterface::setOptions()
	 */
	public function setOptions($options) {

		$this->properties = array_merge ( $this->properties, $options );
		return $this;
	
	}


}
