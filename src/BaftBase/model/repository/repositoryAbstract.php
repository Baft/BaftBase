<?php

namespace baft\model\repository;

use Zend\ServiceManager\ServiceManager;
use Zend\Db\Adapter\Exception\RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;
use baft\model\dao\daoInterface;
use Zend\Db\ResultSet\ResultSet;
use baft\model\reflectionVoHydrator;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\PreparableSqlInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\ArrayObject;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

abstract class repositoryAbstract implements repositoryInterface {
	use \baft\model\entityUtilityTrait;
	
	/**
	 *
	 * @var ServiceLocator
	 */
	protected $serviceLocator;
	protected $sharedDao;

	/**
	 * condtruct repository object and execute __init function (replacement of consructor)
	 *
	 * @param ServiceLocatorInterface $serviceLocator        	
	 * @param array $init_params
	 *        	: parameters that have to passed to __init method
	 */
	final public function __construct(ServiceLocatorInterface $serviceLocator = null, array $init_params = array()) {

		if (isset ( $serviceLocator ))
			$this->setServiceLocator ( $serviceLocator );
		
		if (method_exists ( $this, "__init" ))
			call_user_func_array ( array (
					$this,
					'__init' 
			), $init_params );
	
	}

	/**
	 * each implemented repository can have __init method
	 * in the body of method can do any thing that need to be done
	 * in constructor
	 */
	protected function __init() {

		$this->sharedDao = $this->getDaoInstance ();
	
	}

	/**
	 * set serviceLocator for using on DAOes , so each one can set own adpater
	 * via service
	 *
	 * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {

		$this->serviceLocator = $serviceLocator;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
	 */
	public function getServiceLocator() {

		return $this->serviceLocator;
	
	}

	/**
	 * excute the sqlQuery object by adapter of daoObject
	 * and hydrate to array of voObjects that given from daoObject
	 * good for executing simple query because return vo
	 *
	 * @param daoInterface $dao        	
	 * @param unknown $sql        	
	 * @param HydratorInterface $hydrator
	 *        	: hydrator object , so hydration filters & strategy can be set outside to define how to hydrate to VoObject
	 * @return array of vo`s
	 */
	public function execute(daoInterface $dao, PreparableSqlInterface $selectObject = null, HydratorInterface $hydrator = null) {

		$sql = new Sql ( $dao->getAdapter () );
		$voArray = array ();
		
		if (! isset ( $hydrator ))
			$hydrator = new reflectionVoHydrator ();
		
		if (! isset ( $selectObject ))
			$selectObject = $dao->getSqlQuery ();
			
			// var_dump($sql->getSqlStringForSqlObject($selectObject));
		$statement = $sql->prepareStatementForSqlObject ( $selectObject );
		$result = $statement->execute ();
		
		if ($result->count () != 0)
			foreach ( $result as $row ) {
				$vo = $dao->getVo ();
				$vo = $hydrator->hydrate ( $row, $vo );
				$voArray [] = $vo;
			}
		unset ( $result );
		return $voArray;
	
	}

	/**
	 * like as execute , jusT return association array and good for complex query
	 *
	 * @link execute
	 * @param daoInterface $dao
	 *        	: use to finde right adapter
	 * @param PreparableSqlInterface $selectObject
	 *        	: if is set use instead of `dao query` and exeute
	 * @return array : collector
	 */
	public function executeArray(daoInterface $dao, PreparableSqlInterface $selectObject = null) {

		$sql = new Sql ( $dao->getAdapter () );
		$voArray = array ();
		
		if (! isset ( $selectObject ))
			$selectObject = $dao->getSqlQuery ();
			
			// var_dump($sql->getSqlStringForSqlObject($selectObject));
		$statement = $sql->prepareStatementForSqlObject ( $selectObject );
		$result = $statement->execute ();
		if ($result->count () != 0)
			foreach ( $result as $row ) {
				$voArray [] = $row;
			}
		unset ( $result );
		return $voArray;
	
	}

	abstract public function getDaoInstance();


}
