<?php

namespace reportgen\reportDefenition;

use \Zend\Db\Sql as sql;
use Zend\Db\Sql\Predicate as predicate;

class condition implements sql\Predicate\PredicateInterface, \reportgen\flattenTree\nodeInterface {
	public $name;
	public $desc = '';
	public $alias = '';
	
	/**
	 *
	 * @var PredicateSet
	 */
	public $predicates;

	public function __construct($name) {

		$this->name = $name;
		$this->predicates = array ();
	
	}

	/**
	 *
	 * @param string $name
	 *        	: name of condition
	 * @param array|sql\Predicate\PredicateInterface $predicate        	
	 * @param string $combination
	 *        	: combination of predicateSets in array that passed in argument 2
	 * @return \reportgen\reportDefenition\condition
	 */
	public static function &create($name, $predicate, $combination = "AND") {

		$condition = new static ( $name );
		$condition->addCondition ( $predicate, $combination );
		return $condition;
	
	}

	/**
	 *
	 * @param string $predicates        	
	 * @param string $combination        	
	 * @return \reportgen\reportDefenition\condition
	 */
	public function addCondition($predicates, $combination = "AND") {

		if (empty ( $predicates ))
			throw new \Exception ( "argument 1 passed to condition->addCondition can not be empty" );
		
		if (! is_array ( $predicates ))
			$predicates = array (
					$predicates 
			);
		
		foreach ( $predicates as $predicate ) {
			
			if (! $predicate instanceof predicate\PredicateInterface)
				throw new \Exception ( sprintf ( "argument 1 passed to \"%s\" must be implemented Predicate\\PredicateInterface or sql\\Exception\\ExceptionInterface", __FUNCTION__ ) );
			
			$this->predicates [] = array (
					'predicate' => $predicate,
					"combination" => $combination 
			);
		}
		return $this;
	
	}

	public function getName() {

		return $this->name;
	
	}

	public function getPredicateContianer() {

		return $this->predicates;
	
	}

	public function setDesc($desc) {

		$this->desc = $desc;
		return $this;
	
	}

	public function getDesc($desc) {

		return $this->desc;
	
	}

	public function setAlias($alias) {

		$this->alias = $alias;
		return $this;
	
	}

	public function getAlias($alias) {

		return $this->alias;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Sql\ExpressionInterface::getExpressionData()
	 */
	public function getExpressionData() {

		$PS = new predicate\PredicateSet ();
		if (! empty ( $this->predicates ))
			foreach ( $this->predicates as $predicate )
				$PS->addPredicate ( $predicate ['predicate'], $predicate ['combination'] );
		$arr = $PS->getExpressionData ();
		array_unshift ( $arr, '(' );
		array_push ( $arr, ')' );
		return $arr;
	
	}


}
