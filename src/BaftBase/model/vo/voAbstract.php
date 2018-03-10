<?php

namespace baft\model\vo;

abstract class voAbstract implements voInterface {

	/**
	 * Returns whether the requested key exists
	 *
	 * @param mixed $key        	
	 * @return boolean
	 */
	public function __isset($key) {

		$reflection = new \ReflectionClass ( $this );
		$prop = $reflection->getProperty ( $key );
		if ($prop instanceof \ReflectionProperty) {
			$getter = "get" . ucfirst ( $prop->getName () );
			$value = $this->{$getter} ();
			if ($value != null)
				return true;
		}
		
		return false;
	
	}

	/**
	 * Creates a copy of the ArrayObject.
	 *
	 * @return array
	 */
	public function toArray() {

		$array = array ();
		$reflection = new \ReflectionClass ( $this );
		$props = $reflection->getProperties ( \ReflectionProperty::IS_PRIVATE );
		// $props=get_object_vars($this);
		foreach ( $props as $prop ) {
			$key = $prop->getName ();
			$getter = "get" . ucfirst ( $key );
			$array [$key] = $this->{$getter} ();
		}
		return $array;
	
	}

	/**
	 * Create a new iterator from an ArrayObject instance
	 *
	 * @return \Iterator
	 */
	final public function getIterator() {

		$class = '\ArrayIterator';
		
		return new $class ( $this );
	
	}

	/**
	 * Serialize an ArrayObject
	 *
	 * @return string
	 */
	public function serialize() {

		$propsArray = $this->toArray ();
		return serialize ( $propsArray );
	
	}

	/**
	 * Unserialize an ArrayObject
	 *
	 * @param string $data        	
	 * @return void
	 */
	public function unserialize($data) {

		$array = unserialize ( $data );
		foreach ( $array as $key => $value ) {
			$this->{$key} = $value;
		}
	
	}


}
