<?php

namespace baft\model\dao;

use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\Feature as TableGatewayFeature;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Sql\Predicate\PredicateSet;
use baft\model\reflectionVoHydrator;
use Zend\Db\Sql\Sql;
use baft\model\vo\voInterface;
use Zend\Form as form;
use Zend\Db\Sql\SqlInterface;

abstract class daoAbstract implements daoInterface {
	
	/**
	 *
	 * @var TableGateway
	 */
	protected $tableGateway;
	
	/**
	 *
	 * @var \Zend\Db\Sql\Select
	 */
	protected $selectObject;
	
	/**
	 *
	 * @var \Zend\Db\Adapter\Adapter
	 */
	protected $adapter;

	public function __construct($adapter, $voData = null) {

		$this->adapter = $adapter;
		$this->__init ();
		$vo = $this->getVo ( $voData );
		$table = new TableIdentifier ( $vo::TABLE, $vo::SCHEMA );
		$this->tableGateway = new TableGateway ( $table, $this->adapter );
		$this->tableGateway->getAdapter ()->getPlatform ()->setDriver ( $this->adapter->getDriver () );
		$this->selectObject = $this->getSelect ();
	
	}

	/**
	 * hydtation the data to vo object
	 *
	 * @see \baft\model\dao\daoInterface::toVo()
	 */
	public function toVo($data, $hydrator = null) {

		$vo = $this->getVo ();
		
		if ($data instanceof voInterface)
			$data = $data->toArray ();
		
		if (! is_array ( $data ))
			throw new \Exception ( "toVo conversion expect parameter one to be 'voInterface/array' , type of '" . gettype ( $hydrator ) . "' passed" );
		
		if ($hydrator instanceof HydratorInterface)
			return $hydrator->hydrate ( $data, $vo );
		
		if (is_array ( $hydrator ))
			return (new reflectionVoHydrator ())->setDataMapper ( $hydrator )->hydrate ( $data, $vo );
		
		throw new \Exception ( "toVo conversion expect parameter two to be 'HydratorInterface/array' , type of '" . gettype ( $hydrator ) . "' passed" );
	
	}

	/**
	 * excute the sqlQuery object by adapter of daoObject
	 * and hydrate to array of voObjects that given from daoObject
	 * good for executing simple query because return vo
	 *
	 * @param PreparableSqlInterface $sql        	
	 * @param HydratorInterface $hydrator
	 *        	: hydrator object , so hydration filters & strategy can be set outside to define how to hydrate to VoObject
	 * @return array of vo`s
	 */
	public function execute(PreparableSqlInterface $selectObject = null, HydratorInterface $hydrator = null) {

		$sql = new Sql ( $this->getAdapter () );
		$voArray = array ();
		
		if (! isset ( $hydrator ))
			$hydrator = new reflectionVoHydrator ();
		
		if (! isset ( $selectObject ))
			$selectObject = $this->getSqlQuery ();
			
			// var_dump($sql->getSqlStringForSqlObject($selectObject));
		$statement = $sql->prepareStatementForSqlObject ( $selectObject );
		$result = $statement->execute ();
		// $sqlString= $sql->getSqlStringForSqlObject($selectObject);
		// $result = $sql->getAdapter()->query($sqlString,Adapter::QUERY_MODE_EXECUTE);
		
		if ($result->count () != 0)
			foreach ( $result as $row ) {
				$vo = $this->getVo ();
				$vo = $hydrator->hydrate ( $row, $vo );
				$voArray [] = $vo;
			}
		unset ( $result );
		return $voArray;
	
	}

	/**
	 * find by column
	 * if methode name findBy`$column` is exist ,execute it
	 *
	 * @see \baft\model\dao\daoInterface::findBy()
	 * @return daoAbstract | mixed
	 */
	public function findBy($column, $value = null) {

		$method = "findBy" . ucfirst ( $column );
		if (method_exists ( $this, $method )) {
			if (! is_array ( $value ))
				$value = array (
						$value 
				);
			return call_user_func_array ( array (
					$this,
					$method 
			), $value );
		} else
			return $this->find ( array (
					$column => $value 
			), [ 
					'*' 
			] );
	
	}

	public function voProxy($vo) {
		// @TODO get vo object read metadata to find relation of vo to the
		// his own vo , then try to fill relationalKeys with related Vo
		// due to many-many/one-many/one-one relation
	}

	/**
	 * wrappd by AbstractParent::findBy
	 *
	 * @param unknown_type $column        	
	 * @param string|array $value        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function findAll() {

		return $this->find ( ' TRUE ', [ 
				'*' 
		], $this->getSelect () );
	
	}

	/**
	 *
	 * @param string $predicates
	 *        	: where condition same as select->where($condition)
	 * @param array $column
	 *        	default is *
	 * @return Ambigous <\Zend\Db\Sql\Select, \Zend\Db\Sql\Select>
	 */
	public function find($predicates = ' TRUE ', array $column = array(
			'*'
	), \Zend\Db\Sql\Select $sqlObject = null) {

		if (isset ( $sqlObject ))
			$this->selectObject = $sqlObject;
		
		$this->selectObject->columns ( $column )->where ( $predicates );
		
		return $this;
	
	}

	final public function getSqlQuery($newInstance = false) {

		if ($newInstance)
			$this->selectObject = $this->getSelect ();
		return $this->selectObject;
	
	}

	/**
	 *
	 * @see \Application\Model\Entity\InterfaceDataAccess::getAdapter()
	 */
	public function getAdapter() {

		return $this->tableGateway->getAdapter ();
	
	}

	/**
	 * return connection object due to transactional behavior
	 *
	 * @return \Zend\Db\Adapter\Driver\ConnectionInterface
	 */
	public function getConnection() {

		return $this->getAdapter ()->getDriver ()->getConnection ();
	
	}

	/**
	 * return new select object from table of the dao
	 *
	 * @return \Zend\Db\Sql\Select
	 */
	public function getSelect() {

		$select = $this->tableGateway->getSql ()->select ();
		if (strcasecmp ( $this->getAdapter ()->getPlatform ()->getName (), "db2v7" ) == 0)
			$select->setSpecification ( $select::LIMIT, 'FETCH FIRST %1$s ROWS ONLY' );
		return $select;
	
	}

	/**
	 * convert sql object to string ,
	 * if dao passed fetch sql object of dao ,
	 * if null passed fetch sql object of this dao ,
	 * if sql object passed convert it
	 *
	 * @param daoInterface|SqlInterface|null $queryObject        	
	 * @return \Zend\Db\Sql\Platform\mixed
	 */
	public function getSqlString($queryObject = null) {

		$sql = new Sql ( $this->getAdapter () );
		$platform = $this->tableGateway->getAdapter ()->getPlatform ();
		
		// if $queryObject is dao
		if ($queryObject instanceof daoInterface)
			$queryObject = $queryObject->getSqlQuery ();
			
			// if $queryObject is null
		if (! isset ( $queryObject ))
			$queryObject = $this->getSqlQuery ();
		return $sql->getSqlStringForSqlObject ( $queryObject, $platform );
	
	}

	/**
	 * wrap the argument as "from" clause and return new wrapper select statement
	 *
	 * @param array $fromQuery        	
	 * @return \Zend\Db\Sql\Select
	 */
	public function getSelectWrapper($fromQuery) {

		if (! is_array ( $fromQuery ) || empty ( $fromQuery ))
			throw new \Exception ( "first parameter pass to select wrapper is \"from\" clause and expect to be associative array" );
		
		$wrapper = new Sql ( $this->getAdapter () );
		$select = $wrapper->select ()->from ( $fromQuery )->columns ( array (
				"*" 
		) );
		return $select;
	
	}

	public function insert(array $set) {

		return $this->tableGateway->insert ( $set );
	
	}

	public function update(array $set, $where = null) {

		return $this->tableGateway->update ( $set, $where );
	
	}

	public function delete($where) {

		return $this->tableGateway->delete ( $where );
	
	}

	public function filterBy($predicates = ' TRUE ', $combination = PredicateSet::OP_AND, \Zend\Db\Sql\Select $sqlObject = null) {

		$where = new Where ();
		
		if (isset ( $sqlObject ))
			$this->selectObject = $sqlObject;
		
		if ($predicates instanceof voInterface) {
			$vo = $predicates;
			$vo = $vo->toArray ();
		}
		
		$this->selectObject->where ( $predicates, $combination );
		
		return $this;
	
	}

	public function toForm() {

		$vo = $this->getVo ();
		$formFactory = new form\Factory ();
		return $formFactory->createForm ( $vo );
	
	}

	/**
	 *
	 * @param array $columns        	
	 * @return unknown
	 */
	public function sortBy($orderBy = '', \Zend\Db\Sql\Select $sqlObject = null) {

		if (! isset ( $sqlObject ))
			$sqlObject = $this->selectObject;
		
		if (! empty ( $orderBy )) {
			$sqlQuery = $sqlObject->order ( $orderBy );
		}
		
		return $this;
	
	}


}
