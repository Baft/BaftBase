<?php

namespace reportgen;

use reportgen\dataGateway\reportDataGateway;

class reportgen {

	public function getReport($repName) {

		$class = '\reportgen\reportDefenition\reports\\' . $repName;
		if (class_exists ( $class )) {
			$report = new $class ();
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

		$dataGateway = new reportDataGateway ( $report );
		$report = $dataGateway->hydrate ( $data, $report );
		return $report;
	
	}


}
