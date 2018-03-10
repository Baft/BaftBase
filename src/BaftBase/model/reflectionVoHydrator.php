<?php

namespace baft\model;

use Zend\Stdlib\Hydrator\Reflection;

class reflectionVoHydrator extends AbstractHydratorByFilter {
	
	/**
	 * Simple in-memory array cache of ReflectionProperties used.
	 *
	 * @var array
	 */
	protected static $reflProperties = array ();
	protected $dataMapper = array ();

	/**
	 * Extract values from an object
	 *
	 * @param object $object        	
	 * @return array
	 */
	public function extract($object) {

		$result = array ();
		foreach ( self::getReflProperties ( $object ) as $property ) {
			$propertyName = $property->getName ();
			if (! $this->filterComposite->filter ( $propertyName )) {
				continue;
			}
			
			$value = $property->getValue ( $object );
			$value = $this->extractValue ( $propertyName, $value );
			$result [$propertyName] = $this->extractValueByFilter ( $propertyName, $value );
		}
		
		return $result;
	
	}

	/**
	 * Hydrate $object with the provided $data.
	 *
	 * @param array $data        	
	 * @param object $object        	
	 * @return object
	 */
	public function hydrate(array $data, $object) {

		$reflProperties = self::getReflProperties ( $object );
		foreach ( $data as $key => $value ) {
			// $key=strtolower($key);
			
			// if datamapper set this key to a correspond property of object , replace key with its correspond
			if (isset ( $this->dataMapper [$key] ))
				$key = $this->dataMapper [$key];
			
			if (isset ( $reflProperties [$key] )) {
				$value = $this->hydrateValue ( $key, $value );
				$value = $this->hydrateValueByFilter ( $key, $value );
				$reflProperties [$key]->setValue ( $object, $value );
			}
		}
		return $object;
	
	}

	/**
	 * with setting data mapper , hydrator can create/map input data to specific object
	 * a data mapper is an array (key-value) ,
	 * data mapper keys are aggregated indexes in input data
	 * data mapper value is coresponding index in destination object
	 *
	 * @param array $dataMapper        	
	 */
	public function setDataMapper(array $dataMapper) {

		$this->dataMapper = $dataMapper;
	
	}

	/**
	 * Get a reflection properties from in-memory cache and lazy-load if
	 * class has not been loaded.
	 *
	 * @static
	 *
	 * @param string|object $input        	
	 * @throws Exception\InvalidArgumentException
	 * @return array
	 */
	protected static function getReflProperties($input) {

		if (is_object ( $input )) {
			$input = get_class ( $input );
		} elseif (! is_string ( $input )) {
			throw new Exception\InvalidArgumentException ( 'Input must be a string or an object.' );
		}
		
		if (! isset ( static::$reflProperties [$input] )) {
			$reflClass = new \ReflectionClass ( $input );
			$reflProperties = $reflClass->getProperties ();
			
			foreach ( $reflProperties as $property ) {
				$property->setAccessible ( true );
				static::$reflProperties [$input] [$property->getName ()] = $property;
			}
		}
		
		return static::$reflProperties [$input];
	
	}


}
