<?php

namespace baft\model;

use Zend\Stdlib\ArrayObject;

class collector extends ArrayObject {

	public function __construct(array $storage) {

		parent::__construct ( $storage );
	
	}

	/**
	 * get type of collector elements
	 *
	 * @return string : class name
	 */
	public function getLiableType() {

		if (! $this->offsetExist ( 0 ))
			throw new \Exception ( "collector is empty" );
		
		return get_class ( $this->offsetGet ( 0 ) );
	
	}

	public function getCurent() {

	}


}
