<?php

namespace reportgen\dataGateway;

use Zend\InputFilter as filter;
use Zend\Stdlib\Hydrator\HydratorInterface;
use reportgen\reportDefenition\reportDefenitionAbstract;
use reportgen\dataGateway\variable;

class reportDataGateway implements HydratorInterface {
	protected $filter;
	protected $inputs = array ();

	public function __construct(array $variables) {

		$this->filter = new filter\InputFilter ();
		foreach ( $variables as $var ) {
			$this->addFilter ( $var );
		}
	
	}

	/**
	 *
	 * @param unknown $data        	
	 * @return Ambigous <multitype:\Zend\InputFilter\InputInterface , multitype:, \Zend\InputFilter\InputFilterInterface, \Zend\InputFilter\InputInterface>
	 */
	public function checkFilterData($data) {

		return $this->getFilter ()->getFactory ()->getDefaultFilterChain ()->filter ( $data );
	
	}

	/**
	 *
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getFilter() {

		return $this->filter;
	
	}

	/* ################################################## */
	/* ################################################## */
	/* ################################################## */
	
	/**
	 * get variables defenition to add to the form
	 *
	 * @param ElementInterface|array $queryVarriable        	
	 */
	public function addFilter($varriable, $name = null) {

		if ($varriable instanceof variable) {
			$name = $varriable->name;
			$varriable = $varriable->toArray ();
		}
		
		if (! empty ( $varriable )) {
			$this->filter->add ( $varriable, $name );
		}
		if (isset ( $varriable ['default'] ))
			$this->filter->get ( $name )->setValue ( $varriable ['default'] );
		return $this;
	
	}

	public function setVariableData($data, $varName) {

		$input = $this->getFilter ()->get ( $varName );
		$input->setData ( $data );
		return $this;
	
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
	 */
	public function extract($report) {

		$vars = $report->getVariables ();
		return $vars;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Stdlib\Hydrator\HydratorInterface::hydrate()
	 */
	public function hydrate(array $data, $report) {

		if (! $this->getFilter ()->setData ( $data )->isValid ()) {
			$massages = "";
			foreach ( $this->getFilter ()->getInvalidInput () as $name => $input )
				$massages .= "<br/>$name : <br/>\t" . implode ( "<br/>\t", $input->getMessages () );
			throw new \Exception ( "data passed to report \"{$report->getName()}\" is not valid . problem is in : {$massages}" );
		}
		
		$values = $this->getFilter ()->getValues ();
		foreach ( $values as $name => $value ) {
			$var = $report->getVariable ( $name );
			try {
				$report->getVariable ( $name )->setData ( $value );
			}
			catch ( \Exception $exc ) {
				throw new \Exception ( $exc->getMessage () . " in report \"{$report->getName()}\"", null, $exc );
			}
		}
		
		return $report;
	
	}


}
