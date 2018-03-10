<?php

namespace BaftBase\std\flattenTree;

interface checkInterface {

	/**
	 *
	 * @param string $nodeName        	
	 * @param array $treeElement        	
	 * @return boolean
	 */
	public static function check($nodeName, &$treeElement);


}