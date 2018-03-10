<?php

namespace reportgen\reportDefenition\reports;

use reportgen\reportDefenition\reportDefenitionAbstract;
use \Zend\Db\Sql as sql;
use \Zend\Db\Sql\Predicate as predicate;
use reportgen\reportDefenition\condition;
use reportgen\dataGateway\variable;

class report2334 extends reportDefenitionAbstract {

	protected function setName() {

		$this->reportId = "report2334";
	
	}

	protected function setTable() {

		$this->report->from ( array (
				"A" => new sql\TableIdentifier ( 'table1' ) 
		) );
	
	}

	protected function setColumns() {

		$this->addColumn ( array (
				"colName1" 
		) );
	
	}

	protected function setConditions() {

		$sub1CondOne = condition::create ( 'sub1CondOne', new predicate\PredicateSet ( [ 
				new predicate\Operator ( 'sub1CondOne', predicate\Operator::OP_EQ, 4 ) 
		] ) );
		
		$sub2CondOne = condition::create ( 'sub2CondOne', new predicate\PredicateSet ( array (
				new predicate\Operator ( 'sub2CondOne', predicate\Operator::OP_EQ, 5 ) 
		) ) );
		
		$sub3CondOne = condition::create ( 'sub3CondOne', new predicate\PredicateSet ( array (
				new predicate\Operator ( 'sub3CondOne', predicate\Operator::OP_EQ, 7 ) 
		) ) );
		
		$sub1sub3CondOne = condition::create ( 'sub1sub3CondOne', new predicate\PredicateSet ( [ 
				new predicate\Operator ( 'sub1sub3CondOne1', predicate\Operator::OP_EQ, 9 ),
				new predicate\Expression ( 'SQR(sub1sub3CondOne2) <> ?', 9 ) 
		], "AND" ) );
		
		$condOne = condition::create ( 'condOne', array (
				new predicate\PredicateSet ( array (
						new predicate\Operator ( 'condOne1', predicate\Operator::OP_EQ, 1 ),
						new predicate\Operator ( 'condOne2', predicate\Operator::OP_EQ, 2 ) 
				), "OR" ),
				new predicate\PredicateSet ( [ 
						new predicate\Expression ( 'condOne3 > ?', $this->varName2 ) 
				] ) 
		), "OR" );
		
		$this->addWhereCondition ( new predicate\Operator ( 'fix1', predicate\Operator::OP_EQ, 9 ), "OR" )
			->addWhereCondition ( new predicate\Operator ( 'fix2', predicate\Operator::OP_EQ, 9 ), "OR" )
			->addWhereCondition ( $condOne, "AND" )
			->addWhereCondition ( $sub1CondOne, $condOne, "AND" )
			->addWhereCondition ( $sub2CondOne, $condOne, "OR" )
			->addWhereCondition ( $sub3CondOne, $condOne, "OR" )
			->addWhereCondition ( $sub1sub3CondOne, $sub3CondOne, "OR" )
			->
		// ->removeWhereCondition($condOne)
		addHavingCondition ( $sub2CondOne )
			->addHavingCondition ( new predicate\Operator ( 'col5', predicate\Operator::OP_EQ, $this->varName1 ) )
			->removeHavingCondition ( $sub2CondOne );
		
		// var_dump($this->packWhereCondition());
	}

	/**
	 * call after intial and befor eny thing create
	 *
	 * @see \reportgen\reportDefenition\reportDefenitionAbstract::setVariables()
	 */
	protected function setVariables() {

		$host = &$this;
		$this->variables = array (
				'varName1' => variable::create ( 'varName1', array (
						function ($value) use($host) {
							// var_dump($host->getColumns());
							
							return $value;
						} 
				), array (
						array (
								'name' => 'EmailAddress' 
						) 
				) )->setOptions ( array (
						'required' => true,
						'allow_empty' => true 
				) ),
				'varName2' => variable::create ( 'varName2', array (), array () )->setOptions ( array (
						'required' => true,
						'allow_empty' => true 
				) ) 
		);
	
	}

	public function getFrom() {

		return array (
				'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
				'elements' => array (
						array (
								'spec' => array (
										'name' => 'varName2',
										'options' => array (
												'label' => 'Your name' 
										),
										'type' => 'Text' 
								) 
						),
						array (
								'spec' => array (
										'type' => 'Zend\Form\Element\Email',
										'name' => 'varName1',
										'options' => array (
												'label' => 'Your email address' 
										) 
								) 
						) 
				),
				'input_filter' => array( /* ... */
				) 
		);
	
	}


}
