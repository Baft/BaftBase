<?php

namespace BaftBase\std\flattenTree;

/**
 * just for ease of use , extend this tree or instansiat it
 * new flattenTree()->init(scopeName is `default` on empty);
 *
 * @author web
 *        
 */
class flattenTree {
	use \BaftBase\std\flattenTree\flattenTreeTrait;

	public function __construct($scopes) {

		return $this->init ( $scopes );
	
	}

	/**
	 * create node obeject
	 * used to capsulate instance objects in node object
	 * when instance are not implementation of nodeInterface
	 *
	 * @param unknown $name        	
	 * @param unknown $instance        	
	 * @return \BaftBase\std\flattenTree\node
	 */
	public static function nodeFactory($name, $instance) {

		$node = new node ( $name );
		$node->setInstance ( $instance );
		return $node;
	
	}


}