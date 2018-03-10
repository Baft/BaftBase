<?php

namespace BaftBase\std\flattenTree;

/**
 * each node of tree have to implement this interface
 *
 * @author web
 *        
 */
interface nodeInterface {

	public function getName();

	public function setInstance($instance);

	public function getInstance();


}