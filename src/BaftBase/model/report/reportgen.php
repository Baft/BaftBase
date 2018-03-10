<?php

namespace reportgen;

use reportgen\dataGateway\reportDataGateway;

class reportgen {

	public function getReport($repName, $dataSource = array()) {

		$class = '\reportgen\reportDefenition\reports\\' . $repName;
		if (class_exists ( $class )) {
			$report = new $class ();
			$report = $this->setVariableData ( $report, $dataSource )->makeReport ();
			return $report;
		}
		throw new \Exception ( "requested report \"$repName\" not exist" );
	
	}

	/**
	 * decouple dataGateway from report
	 *
	 * @param \reportgen\reportDefenition\reportDefenitionAbstract $report        	
	 * @param unknown $data        	
	 * @param \Zend\Form\Form $dataGateway        	
	 */
	public function setVariableData(\reportgen\reportDefenition\reportDefenitionAbstract $report, $data) {

		$variables = $report->getVariables ();
		$dataGateway = new reportDataGateway ( $variables );
		$report = $dataGateway->hydrate ( $data, $report );
		return $report;
	
	}


}
