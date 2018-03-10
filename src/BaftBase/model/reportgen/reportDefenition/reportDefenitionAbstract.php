<?php

namespace reportgen\reportDefenition;

use \Zend\Db\Sql as sql;
use \Zend\Db\Sql\Predicate as predicate;
use \reportgen\dataGateway as dataGateway;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Adapter\Driver\IbmDb2\Statement;
use \reportgen\flattenTree\flattenTree;

/**
 *
 * @todo add a possibility to mark a record and fecth all where conditions (equality condition) to achive that record to send thath record
 * @todo some columns is main in a report so have not to remove them to deny ?
 * @author root
 *        
 */
abstract class reportDefenitionAbstract {
	use \reportgen\flattenTree\flattenTree{init as initTree;}
	const ALL_COL = '*';
	const FIX_REPORT = "fix_report";
	const DYNAMIC_REPORT = "dynamic_report";
	
	/**
	 *
	 * @todo fill the gap
	 */
	protected $reportType;
	
	/**
	 * report body
	 *
	 * @var \Zend\Db\Sql\Select
	 */
	protected $report;
	
	/**
	 *
	 * @todo add UUID
	 *       name of report
	 * @var string
	 */
	protected $reportId;
	
	/**
	 * use to describ field in grids
	 *
	 * @var array
	 */
	protected $columnsAlias = array ();
	
	/**
	 *
	 * @var \Zend\Db\Sql\Where
	 */
	protected $where;
	
	/**
	 *
	 * @var \Zend\Db\Sql\Having
	 */
	protected $having;
	
	/**
	 * define all variables used in query , varName-varType
	 * 'varType' can be nameOfFormElement=String or objec of reportVariableDefine=\reportgen\dateGateway\variable\variableInterface
	 * 'varName' can be null if 'varType' is object of Element and objectName is set
	 *
	 * @var array
	 */
	protected $variables = array ();
	
	/**
	 * use to save variable data , used by form binding to support default values
	 *
	 * @var \Zend\Stdlib\ArrayObject
	 */
	protected $variablesData;

	/**
	 * if report create staitly , these method must be call sequential
	 * 1.instantiate
	 * 2.setVariableData
	 * else report can create by reportgen that automatically create and check
	 *
	 * @param unknown $reportType        	
	 */
	public function __construct($reportType = self::FIX_REPORT) {

		$this->init ( $reportType = self::FIX_REPORT );
		$this->setVariables ();
		// $this->setVariableData();
		
		$this->setName ();
		if (! isset ( $this->reportId ))
			throw new \Exception ( "no name set for report " . get_class ( $this ) );
		$this->setColumns ();
		$this->setTable ();
	
	}

	final public function getReport() {

		$this->setConditions ();
		$whereCondition = $this->packWhereCondition ();
		$this->report->reset ( sql\Select::WHERE );
		$this->report->where ( $whereCondition );
		
		$havingCondition = $this->packHavingCondition ();
		$this->report->reset ( sql\Select::HAVING );
		$this->report->having ( $havingCondition );
		
		return $this;
	
	}

	/**
	 * short call , return variable data
	 *
	 * @param unknown $varName        	
	 */
	public function __get($varName) {

		return $this->getVariableData ( $varName );
	
	}

	/**
	 * set name of report
	 */
	abstract protected function setName();

	/**
	 * return report name
	 *
	 * @return string
	 */
	public function getName() {

		return $this->reportId;
	
	}

	abstract protected function setTable();

	abstract protected function setConditions();

	/**
	 * return sql object
	 *
	 * @return \Zend\Db\Sql\Select
	 */
	public function getSql() {

		return $this->report;
	
	}

	/**
	 * all variables thath used in query , define here
	 */
	abstract protected function setVariables();

	/**
	 * return all variables exist in report
	 *
	 * @return array:
	 */
	public function getVariables() {

		return $this->variables;
	
	}

	/**
	 * retunr type of variable
	 *
	 * @param string $varName        	
	 * @return multitype:|NULL
	 */
	public function &getVariable($varName) {

		if ($this->isExistVariable ( $varName )) {
			return $this->variables [$varName];
		} else
			throw new \Exception ( "requested variable \"{$varName}\" is not exist" );
	
	}

	/**
	 * chekc if variableName is exist
	 *
	 * @param unknown $varName        	
	 * @return boolean
	 */
	public function isExistVariable($varName) {

		if (isset ( $this->variables [$varName] ))
			return true;
		
		return false;
	
	}

	/**
	 * get variable data or all variablesData
	 *
	 * @param string $varName        	
	 * @return multitype|arrayObject
	 */
	public function getVariableData($varName) {

		$variable = $this->getVariable ( $varName );
		if (! isset ( $variable )) // variable has not data or name and is not locked
			throw new \Exception ( "requested variable \"{$varName}\" of report \"{$this->reportId}\" have no data" );
		
		return $variable->getData ();
	
	}

	/**
	 * add column to report
	 *
	 * @param array $columns
	 *        	like select->column
	 * @param string $setPrefix
	 *        	like select->column
	 * @return \reportgen\reportDefenition\reportDefenitionAbstract
	 */
	public function addColumn(array $columns, $setPrefix = true) {

		$oldColumns = $this->report->getRawState ( sql\Select::COLUMNS );
		if (empty ( $oldColumns ))
			$oldColumns = array ();
		$newColumns = array_merge ( $oldColumns, $columns );
		$this->report->reset ( sql\Select::COLUMNS );
		$this->report->columns ( $newColumns, $setPrefix );
		return $this;
	
	}

	/**
	 * remove specific column
	 *
	 * @param string $columnName        	
	 * @return \reportgen\reportDefenition\reportDefenitionAbstract
	 */
	public function removeColumn($columnName) {

		$oldColumns = $this->report->getRawState ( sql\Select::COLUMNS );
		$newColumns = array ();
		// @TODO remove columnAlias whene removing column
		foreach ( $oldColumns as $index => $column ) {
			if (is_numeric ( $index ) && strcasecmp ( $column, $columnName ) != 0)
				$newColumns [] = $column;
			elseif (is_string ( $index ) && strcasecmp ( $index, $columnName ) != 0)
				$newColumns [$index] = $column;
		}
		$this->report->reset ( sql\Select::COLUMNS );
		$this->report->columns ( $newColumns );
		return $this;
	
	}

	/**
	 * get specific column or all columns
	 *
	 * @param string $name        	
	 * @return array|string|\Zend\Db\Sql\Expression
	 */
	public function getColumns($name = null) {
		// @todo check if in array of columns is exist any star , not just in [0] index
		$columns = $this->report->getRawState ( sql\Select::COLUMNS );
		if ($columns [0] != self::ALL_COL) {
			if (isset ( $name ) && isset ( $columns [$name] ))
				return $columns [$name];
			
			$columnsList = array ();
			foreach ( $columns as $index => $column ) {
				if (is_numeric ( $index ))
					$columnsList [] = $column;
				else
					$columnsList [] = $index;
			}
			
			return $columnsList;
		}
		
		return $this->fetchColumns ();
	
	}

	/**
	 * Check if exist columnName
	 *
	 * @param string $columnName
	 *        	: columnName
	 */
	public function isExistColumn($columnName) {

		$columns = $this->getColumns ();
		if (array_search ( $columnName, $columns ))
			return true;
		return false;
	
	}

	public function setColumnAlias($columnName, $alias) {

		$this->columnsAlias [$columnName] = $alias;
		return $this;
	
	}

	public function getColumnAlias($columnName) {

		if (isset ( $this->columnsAlias [$columnName] ))
			return $this->columnsAlias [$columnName];
		
		return $columnName;
	
	}

	/**
	 * conditions must be assosiative array name-value
	 *
	 * @param array|\Zend\Db\Sql\Predicate\PredicateInterface $conditions        	
	 */
	public function addWhereCondition($conditions, $parent = null, $combination = 'AND') {

		if ($parent == sql\Predicate\PredicateSet::OP_AND || $parent == sql\Predicate\PredicateSet::OP_OR) {
			$parent = null;
			$combination = $parent;
		}
		
		if ($conditions instanceof condition) {
			$this->addCondition ( sql\Select::WHERE, $conditions, $parent, $combination );
			
			return $this;
		}
		
		if ($conditions instanceof sql\Predicate\PredicateInterface) {
			
			$predicate = $this->capsulateCondition ( $conditions, $combination );
			$this->where->addPredicate ( $predicate, $combination );
		}
		
		return $this;
	
	}

	public function removeWhereCondition($conditionName) {

		return $this->removeCondition ( sql\Select::WHERE, $conditionName );
	
	}

	/**
	 * conditions must be assosiative array name-value
	 *
	 * @param array|\Zend\Db\Sql\Predicate\PredicateInterface $conditions        	
	 */
	public function addHavingCondition($conditions, $parent = null, $combination = 'AND') {

		if ($parent == sql\Predicate\PredicateSet::OP_AND || $parent == sql\Predicate\PredicateSet::OP_OR) {
			$parent = null;
			$combination = $parent;
		}
		
		if ($conditions instanceof condition) {
			$this->addCondition ( sql\Select::HAVING, $conditions, $parent, $combination );
			return $this;
		}
		
		if ($conditions instanceof sql\Predicate\PredicateInterface) {
			$predicate = $this->capsulateCondition ( $conditions, $combination );
			$this->having->addPredicate ( $predicate, $combination );
		}
		
		return $this;
	
	}

	public function removeHavingCondition($conditionName) {

		return $this->removeCondition ( sql\Select::HAVING, $conditionName );
	
	}

	/* ################################################################# */
	/* ######################## PROTECTEDs ########################### */
	/* ################################################################# */
	protected function init($reportType = self::FIX_REPORT) {

		$this->initTree ( array (
				sql\Select::HAVING,
				sql\Select::WHERE 
		) );
		$this->report = new sql\Select ();
		if (strcasecmp ( $reportType, self::FIX_REPORT ) != 0 && strcasecmp ( $reportType, self::DYNAMIC_REPORT ) != 0)
			throw new \Exception ( "report type is invalid" );
		$this->reportType = $reportType;
		
		$this->where = new sql\Where ();
		$this->wherePredicate = new predicate\PredicateSet ();
		
		$this->having = new sql\Having ();
		$this->havingPredicate = new predicate\PredicateSet ();
		
		$this->variablesData = new \Zend\Stdlib\ArrayObject ();
		$this->report->reset ( sql\Select::COLUMNS );
	
	}

	/**
	 *
	 * @param string $statement        	
	 * @param condition $conditions        	
	 * @param string $parent        	
	 * @param string $combination        	
	 * @throws \Exception
	 * @return \reportgen\reportDefenition\reportDefenitionAbstract
	 */
	final protected function addCondition($statement, condition $conditions, $parent = null, $combination = 'AND') {

		$this->addNode ( $conditions, $parent, $statement );
		$this->addProperty ( $conditions, array (
				'combination' => $combination 
		), $statement );
		
		return $this;
	
	}

	final protected function isExistCondition($statement, $name) {

		return $this->isExistNode ( $name, $statement );
	
	}

	final protected function getConditions($statement, $name = null) {

		return $this->getNode ( $name, $statement );
	
	}

	final protected function packWhereCondition($combination = "AND") {

		$pack = $this->packConditions ( sql\Select::WHERE );
		$checkPackEmpty = $pack->getPredicates ();
		if (! empty ( $checkPackEmpty ))
			$this->where->addPredicate ( $pack, $combination );
		
		return $this->where;
	
	}

	final protected function packHavingCondition($combination = "AND") {

		$pack = $this->packConditions ( sql\Select::HAVING );
		$checkPackEmpty = $pack->getPredicates ();
		if (! empty ( $checkPackEmpty ))
			$this->having->addPredicate ( $pack, $combination );
		
		return $this->having;
	
	}

	/**
	 * pack conditions tree to a predicateSet Flat
	 *
	 * @param string $statement        	
	 * @return \Zend\Db\Sql\Predicate\PredicateSet
	 */
	final protected function packConditions($statement) {

		$statementConditions = &$this->getScope ( $statement );
		$pack = new PredicateSet ();
		
		$host = &$this;
		$concat = function &(array &$root) use(&$host, $statement, &$concat) {
			$children = $root ['children'];
			if (empty ( $children )) {
				return $root ['instance'];
			}
			
			foreach ( $children as $conditionName ) {
				$childProps = $this->getConditions ( $statement, $conditionName );
				$combination = $childProps ['property'] ['combination'];
				
				/**
				 *
				 * @var condition
				 */
				$childPacked = $concat ( $childProps );
				$root ['instance']->addCondition ( $childPacked, $combination );
			}
			
			return $root ['instance'];
		};
		
		foreach ( $statementConditions as $rootName => $rootProp ) {
			if (! isset ( $rootProp ['parent'] ) || empty ( $rootProp ['parent'] )) {
				$rootPacked = $concat ( $statementConditions [$rootName] );
				$combination = $rootProp ['property'] ['combination'];
				$pack->addPredicate ( $rootPacked, $combination );
			}
		}
		return $pack;
	
	}

	final protected function removeCondition($statement, $conditionName) {

		$this->removeNode ( $conditionName, $statement );
		return $this;
	
	}

	/**
	 *
	 * @param array|\Zend\Db\Sql\Predicate\PredicateInterface $conditions        	
	 * @param string $combination        	
	 * @throws \Exception
	 * @return \Zend\Db\Sql\Predicate\Predicate
	 */
	final protected function capsulateCondition($conditions, $combination = 'AND') {

		if (empty ( $conditions ))
			throw new \Exception ( "where conditions can not be empty" );
		
		$predicate = new sql\Predicate\PredicateSet ();
		
		if ($conditions instanceof \Zend\Db\Sql\Predicate\PredicateInterface)
			$conditions = array (
					$conditions 
			);
		
		if (is_array ( $conditions )) {
			foreach ( $conditions as $condition ) {
				$predicate->addPredicate ( $condition, $combination );
			}
		} else
			throw new \Exception ( "conditions must be assosiative arrays name-value , non-array given" );
		return $predicate;
	
	}

	/**
	 *
	 * @todo exceute query ince to fetch columns
	 * @return
	 *
	 */
	protected function fetchColumns() {

	}


}
