<?php

namespace Reportgen\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use reportgen\reportDefenition\reportDefenitionAbstract;

/**
 * just for ease of use of reprtgen class
 *
 * @author root
 *        
 */
class reportGateway extends AbstractPlugin implements ServiceManagerAwareInterface {
	public $reportGen;
	
	/*
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\Plugin\AbstractPlugin::getController()
	 */
	public static $dataAccessControl;

	public function getController() {
		// TODO: Auto-generated method stub
	}

	public function __construct() {

		$this->reportGen = new \reportgen\reportgen ();
		self::$dataAccessControl = new \Zend\Permissions\Acl\Acl ();
	
	}

	/**
	 *
	 * @param unknown $repName        	
	 * @param unknown $dataSource        	
	 * @param unknown $dataAccess        	
	 * @return reportgen\reportDefenition\reportDefenitionAbstract
	 */
	public function make($repName, $dataSource) {

		$report = $this->factory ( $repName, $dataSource );
		return $report;
	
	}

	public function &toAcl($reportObject, $schema = 'public') {

		$reportName = $reportObject->getName ();
		$columns = $reportObject->getColumns ();
		$whereConditions = $reportObject->getWhereConditions ();
		$havingConditions = $reportObject->getHavingConditions ();
		
		self::$dataAccessControl->addResource ( $schema );
		self::$dataAccessControl->addResource ( $reportName, $schema );
		self::$dataAccessControl->addResource ( 'columns', $reportName );
		self::$dataAccessControl->addResource ( 'rows', $reportName );
		
		foreach ( $columns as $name ) {
			self::$dataAccessControl->addResource ( $name, 'columns' );
		}
		
		foreach ( $havingConditions as $name => $condition ) {
			if (strpos ( $name, 'fix_' ) === 0)
				continue;
			self::$dataAccessControl->addResource ( $name, 'rows' );
		}
		
		foreach ( $whereConditions as $name => $condition ) {
			if (strpos ( $name, 'fix_' ) === 0)
				continue;
			self::$dataAccessControl->addResource ( $name, 'rows' );
		}
		
		return self::$dataAccessControl;
	
	}

	public function toString($report, $adapter) {

		return $report->getSql ()->getSqlString ( $adapter );
	
	}

	public function execute($report, $adapter) {
		// @TODO exceute
	}

	/**
	 * factory report
	 *
	 * @param unknown $repName        	
	 */
	public function factory($repName, $dataSource) {

		return $this->reportGen->getReport ( $repName, $dataSource );
	
	}

	/**
	 * inject data to report
	 * split data filling from constructing to achive portability and reuseability
	 *
	 * @param unknown $report        	
	 * @param unknown $dataResource        	
	 */
	public function dataInjection($report, $dataResource) {

		return $this->reportGen->setVariableData ( $report, $dataResource );
	
	}

	/**
	 * controll data access on report
	 *
	 * @param unknown $report        	
	 * @param unknown $dataAccessObject        	
	 * @throws \Exception
	 * @return unknown
	 */
	public function dataAccessControl($report, $role) {

		$reportName = $report->getName ();
		$columns = $report->getColumns ();
		$whereConditions = $report->getWhereConditions ();
		$havingConditions = $report->getHavingConditions ();
		$dataAccessObject = self::$dataAccessControl;
		
		if (! $dataAccessObject->isAllowed ( $role, $reportName ))
			throw new \Exception ( "access denied on report \"{$reportName}\"" );
		
		foreach ( $columns as $index => $column ) {
			if ($dataAccessObject->hasResource ( $column ) && ! $dataAccessObject->isAllowed ( $role, $column ))
				$report->removeColumn ( $column );
		}
		
		foreach ( $whereConditions as $name => $prep ) {
			if ($dataAccessObject->hasResource ( $name ) && ! $dataAccessObject->isAllowed ( $role, $name ))
				$report->removeWhereCondition ( $name );
		}
		
		foreach ( $havingConditions as $name => $prep ) {
			if ($dataAccessObject->hasResource ( $name ) && ! $dataAccessObject->isAllowed ( $role, $name ))
				$report->removeHavingCondition ( $name );
		}
		
		$report = $report->makeSql ();
		
		return $report;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\ServiceManagerAwareInterface::setServiceManager()
	 */
	public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager) {

	}


}
