<?php

namespace Application\Controller;

use Zend\Filter\FilterInterface;

class jalaliDateFilter implements FilterInterface {
	
	/**
	 *
	 * @var array
	 */
	protected $options = array (
			'delimeter' => null 
	);

	/**
	 * Sets filter options
	 *
	 * @param string|array|Traversable $charlistOrOptions        	
	 */
	public function __construct($options = null) {

		if (isset ( $options ['delimeter'] ))
			$this->options ['delimeter'] = $options ['delimeter'];
	
	}

	/**
	 * convert jalali date to geregorian unix time
	 *
	 * @see \Zend\Filter\FilterInterface::filter()
	 */
	public function filter($value = null) {

		if (empty ( $value ))
			return null;
		$jdf = new \BaftBase\jdf ();
		$jalaliDate = [ ];
		$value = trim ( $value );
		try {
			preg_match_all ( "/^([8-9][0-9]|1[3-4][0-9][0-9])[\-\\/\. ]{1}([0-9]|[0][1-9]|[1][0-2])[\-\\/\. ]{1}([0-9]|[1-2][0-9]|[3][0-1])$/", $value, $jalaliDate, PREG_SET_ORDER );
			if (empty ( $jalaliDate ))
				return 0;
			$jalaliDate = $jalaliDate [0];
			if (! isset ( $jalaliDate [1] ) || ! isset ( $jalaliDate [3] ) || ! isset ( $jalaliDate [2] ))
				return 0;
		}
		catch ( \Exception $ex ) {
			return 0;
		}
		$jYear = $jalaliDate [1];
		if ($jYear / 10 < 10)
			$jYear = sprintf ( "13%s", $jYear );
		$jMonth = $jalaliDate [2];
		$jDay = $jalaliDate [3];
		$gDate = $jdf->jalali_to_gregorian ( $jYear, $jMonth, $jDay );
		$gUnix = mktime ( 0, 0, 0, $gDate [1], $gDate [2], $gDate [0] );
		
		return $gUnix;
	
	}


}
