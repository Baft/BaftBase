<?php

namespace baft\model\dao;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator\AdapterAggregateInterface;

interface daoInterface {

	/**
	 * initial the dao , like making vo object or any thing that need to be done in constructor
	 */
	public function __init();

	/**
	 * get new VoObject of dao
	 *
	 * @param
	 *        	mixed : use to initaliaze a vo by default values in dao , use toVo($data) to initalize in the body
	 * @return \baft\model\vo\voInterface
	 */
	public function getVo($data = null);

	/**
	 * hydrate the data to a vo object that geven from getVo
	 *
	 * @param unknown $data        	
	 * @param \baft\model\AbstractHydratorByFilter $hydrator
	 *        	: customize hydration by its fileters/validators
	 */
	public function toVo($data, $hydrator = null);

	/**
	 * select in table by $column where its value is in $value
	 *
	 * @param string $column        	
	 * @param string|array $value        	
	 */
	public function findBy($column, $value);

	public function insert(array $set);

	public function update(array $set, $where = null);

	public function delete($where);

	public function getAdapter();


}