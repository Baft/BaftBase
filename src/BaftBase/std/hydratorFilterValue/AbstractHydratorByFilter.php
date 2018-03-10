<?php

namespace baft\model;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use baft\model\filter\filterValueInterface;

/**
* filtering by values during hydration/extraction
*/

abstract class AbstractHydratorByFilter extends AbstractHydrator {
	protected $filterValueComposite;
	
	/**
	 * save index of input data with its correspond property name in destination object
	 *
	 * @var array
	 */
	protected $dataMapper = array ();

	public function __construct() {

		parent::__construct ();
		$this->filterValueComposite = new FilterComposite ();
	
	}

	/**
	 *
	 * Add a new filter to take care of what needs to be hydrated.
	 * To exclude e.g. the method getServiceLocator:
	 *
	 * <code>
	 * $composite->addFilter("servicelocator",
	 * function($property) {
	 * list($class, $method) = explode('::', $property);
	 * if ($method === 'getServiceLocator') {
	 * return false;
	 * }
	 * return true;
	 * }, FilterComposite::CONDITION_AND
	 * );
	 * </code>
	 *
	 * @param string $name
	 *        	Index in the composite
	 * @param callable|Filter\FilterInterface $filter        	
	 * @param int $condition        	
	 * @return Filter\FilterComposite
	 */
	public function addFilterValue($name, $filter, $condition = FilterComposite::CONDITION_OR) {

		if (! is_callable ( $filter ) && ! ($filter instanceof filterValueInterface)) {
			throw new \InvalidArgumentException ( 'The value of ' . $name . ' should be either a callable or ' . 'an instance of baft\model\filter\filterValueInterface' );
		}
		$this->filterValueComposite->addFilter ( $name, $filter, $condition );
		$this->filterValueStrategy [$name] = $filter;
		return $this;
	
	}

	/**
	 * Check whether a specific filter exists at key $name or not
	 *
	 * @param string $name
	 *        	Index in the composite
	 * @return bool
	 */
	public function hasFilterValue($name) {

		if ($this->filterValueComposite->hasFilter ( $name ) && isset ( $this->filterValueStrategy [$name] ))
			return true;
		return false;
	
	}

	public function hydrate(array $data, $object) {

	}

	public function extract($object) {
		// TODO Auto-generated method stub
	}

	/**
	 * with setting data mapper, hydrator can create/map input data to specific object
	 * a data mapper is an array (key-value) ,
	 * data mapper keys are aggregated indexes in input data
	 * data mapper value is coresponding index in destination object
	 *
	 * @param array $dataMapper        	
	 */
	public function setDataMapper(array $dataMapper) {

		$this->dataMapper = $dataMapper;
	
	}

	public function getDataMapper() {

		return $this->dataMapper;
	
	}

	/**
	 * Remove a filter from the composition.
	 * To not extract "has" methods, you simply need to unregister it
	 *
	 * <code>
	 * $filterComposite->removeFilter('has');
	 * </code>
	 *
	 * @param
	 *        	$name
	 * @return Filter\FilterComposite
	 */
	public function removeFilterValue($name) {

		if (! $this->hasFilterValue ( $name ))
			throw new \Exception ( "filterValue \"$name\" dose not exist " );
		
		$this->filterValueComposite->removeFilter ( $name );
		unset ( $this->filterValueStrategy [$name] );
		return $this;
	
	}

	public function hydrateValueByFilter($name, $value) {

		if (! $this->hasFilterValue ( $name ))
			return $value;
		
		if ($this->filterValueComposite->filter ( $name ))
			return $this->filterValueStrategy [$name]->hydrate ( $value );
		
		return $value;
	
	}

	public function extractValueByFilter($name, $value) {

		if (! $this->hasFilterValue ( $name ))
			return $value;
		
		if (! $this->filterValueComposite->filter ( $name ))
			return $this->filterValueStrategy [$name]->extract ( $value );
		
		return $value;
	
	}


}
