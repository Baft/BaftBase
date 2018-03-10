<?php

namespace baft\model\repository;

use Zend\Paginator\AdapterAggregateInterface;
use Zend\Paginator\Paginator;
use baft\model\dao\daoAbstract;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use baft\model\reflectionVoHydrator;
use Zend\Db\ResultSet\ResultSet;

/**
 * pginator adapter due to ddd model
 *
 * @author root
 *        
 */
class paginationRepository extends repositoryAbstract implements AdapterAggregateInterface {
	const RETURN_ARRAY = "array";
	const RETURN_OBJECT = "object";
	
	/**
	 *
	 * @var daoAbstract
	 */
	protected $paginationDao;
	protected $rowWalkCallback;
	protected $returnType;

	/*
	 * (non-PHPdoc)
	 * @see \baft\model\repository\repositoryAbstract::getDaoInstance()
	 */
	public function getDaoInstance() {
		// TODO Auto-generated method stub
	}

	/**
	 *
	 * @param
	 *        	paginationDao data accessor
	 * @param
	 *        	clauser rowCallback : what to do on each row
	 * @param string $returnType
	 *        	what type have to return when fetching item by getItem()
	 * @see \baft\model\repository\repositoryAbstract::__init()
	 */
	protected function __init($paginationDao, callable $rowCallback = null, $returnType = self::RETURN_OBJECT) {

		$this->paginationDao = $paginationDao;
		$this->returnType = $returnType;
		if (isset ( $rowCallback ))
			$this->setRowWalkCallback ( $rowCallback );
	
	}

	public function setRowWalkCallback(callable $rowCallback) {

		$this->rowWalkCallback = $rowCallback;
	
	}

	public function count() {

		$orginalSelect = $this->paginationDao->getSqlQuery ();
		$orginalSelect->reset ( $orginalSelect::LIMIT )->reset ( $orginalSelect::OFFSET );
		$wrapperSelect = $this->paginationDao->getSelectWrapper ( [ 
				"orginal" => $orginalSelect 
		] );
		$wrapperSelect->columns ( array (
				'c' => new Expression ( 'COUNT(1)' ) 
		) );
		
		$sql = new Sql ( $this->paginationDao->getAdapter () );
		// print $this->paginationDao->getSqlString($wrapperSelect);
		$statement = $sql->prepareStatementForSqlObject ( $wrapperSelect );
		$result = $statement->execute ();
		$row = $result->current ();
		return $row ['c'];
	
	}

	/**
	 * return items in a page
	 *
	 * @param integer $offset        	
	 * @param integer $itemCountPerPage        	
	 * @param callable $rowWalkCallback
	 *        	: like callable parameter in the array_walk
	 * @param string $hydrator
	 *        	: using on excution
	 * @return Ambigous <\Zend\Db\ResultSet\ResultSet, multitype:object >
	 */
	public function getItems($offset = 0, $itemCountPerPage = 10, $hydrator = null) {

		$this->paginationDao->getSqlQuery ()->offset ( $offset );
		
		$this->paginationDao->getSqlQuery ()->limit ( $itemCountPerPage );
		
		switch ($this->returnType) {
			case self::RETURN_OBJECT :
				$result = $this->execute ( $this->paginationDao, null, $hydrator );
				break;
			case self::RETURN_ARRAY :
				$sql = new Sql ( $this->paginationDao->getAdapter () );
				$voArray = array ();
				
				$selectObject = $this->paginationDao->getSqlQuery ();
				
				$statement = $sql->prepareStatementForSqlObject ( $selectObject );
				$result = $statement->execute ();
				$resultSet = new ResultSet ();
				$resultSet->initialize ( $result );
				$result = $resultSet->toArray ();
				break;
		}
		
		if (isset ( $this->rowWalkCallback ))
			array_walk ( $result, $this->rowWalkCallback );
		
		return $result;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Paginator\AdapterAggregateInterface::getPaginatorAdapter()
	 */
	public function getPaginatorAdapter() {

		return $this;
	
	}

	public function paginatDb2V7($provider, $page, $adapter = null, $uniqueColumn = "*") {

		$providerAdapter = null;
		
		if (is_array ( $provider ) && isset ( $provider ["adapter"] ) && isset ( $provider ["provider"] )) {
			$providerAdapter = $provider ["adapter"];
			$provider = $provider ["provider"];
		} elseif (isset ( $adapter ))
			$providerAdapter = $adapter;
		
		$paginAdapter = new db2_paging ( $provider, $providerAdapter, $uniqueColumn );
		$pgIns = new \Zend\Paginator\Paginator ( $paginAdapter );
		// $pgIns->setCurrentPageNumber($page);
		// $pgIns->setPageRange(100);
		// $pgIns->setView("partial/paging.phtml");
		return $pgIns;
	
	}

	/**
	 * return paginator object
	 *
	 * @param string $adapter        	
	 * @param unknown $config        	
	 * @return \Zend\Paginator\Paginator
	 */
	public function getPaginator($adapter = null, $config = array()) {

		if (! empty ( $config ))
			Paginator::setGlobalConfig ( $config );
		if (! isset ( $adapter ))
			$adapter = $this;
		return new Paginator ( $adapter );
	
	}


}
