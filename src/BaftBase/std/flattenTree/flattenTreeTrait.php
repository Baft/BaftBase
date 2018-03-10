<?php

namespace BaftBase\std\flattenTree;

use BaftBase\std\flattenTree\nodeInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Stdlib\SplStack;

trait flattenTreeTrait {
	// shuld be trait to add functionality (not type) to client
	
	/**
	 * link list , each elemetn is
	 * [
	 * treeScopeName =>[
	 * 'instance'=>instanceOfNode,
	 * 'parent'=>nameOfParentNode,
	 * 'children'=>array( childNodeName1 ,childNodeName2 , ... ),
	 * 'property'=>array(everything)
	 * ]
	 * ]
	 *
	 * @var array
	 */
	protected $trees = array ();

	/**
	 * create scopes first
	 * scope is a container for tree
	 * calling a scope give you the tree
	 *
	 * @param array $scope        	
	 */
	public function init($scopes = '') {

		if (! is_string ( $scopes ) && ! is_array ( $scopes ))
			throw new \Exception ( 'flatternTree->init($scopes) expect scope name to be `string` or `array` of strings, type of ' . gettype ( $scopes ) . ' given .' );
		
		if (empty ( $scopes )) {
			$this->setScope ( 'default' );
			return $this;
		}
		
		if (is_string ( $scopes ))
			$scopes = array (
					$scopes 
			);
		
		foreach ( $scopes as $scopeName ) {
			$this->setScope ( $scopeName );
		}
		
		return $this;
	
	}

	/**
	 * add node like this
	 * [
	 * 'default' <-- if leave null for scope
	 * =>[
	 * 0=>[
	 * 'instance'=>instanceOfNode,
	 * 'parent'=>nameOfParentNode,
	 * 'children'=>array( childNodeName1 ,childNodeName2 , ... )
	 * 'property'=>array(everything)
	 * ]
	 * 1=> ...
	 * .
	 * .
	 * .
	 * ],
	 * 'otherScopeName1'=> ...
	 * ]
	 *
	 * @param nodeInterface $node
	 *        	: instance of nodeInterface that caree one instance of every object by node->setInstance
	 * @param string $parent        	
	 * @param string $scope        	
	 * @throws \Exception
	 * @return \reportgen\reportDefenition\reportDefenitionAbstract
	 */
	public function addNode($node, $parent = null, $scope = 'default') {

		$scopeTree = &$this->getScope ( $scope );
		if (! $node instanceof nodeInterface)
			throw new \Exception ( 'addNode() expects $node to be of type `nodeInterface`' );
		
		$nodeName = $node->getName ();
		
		if ($this->isExistNode ( $node, $scope )) {
			throw new \Exception ( "node '$nodeName' already exists in the tree" );
		}
		
		$parentName = null;
		if (null !== $parent && ! empty ( $parent )) {
			
			$parent = &$this->getNode ( $parent, $scope );
			$parent ['children'] [$nodeName] = $nodeName;
			$parentName = $parent ['instance']->getName ();
			// $scopeTree[$parentNodeName]['children'][$nodeName] = $nodeName;
		}
		
		$scopeTree [$nodeName] = array (
				'instance' => $node,
				'parent' => $parentName,
				'children' => array (),
				'property' => array () 
		);
		
		return $this;
	
	}

	/**
	 * set/resset $parent for $node , parent and node have to exist in scope
	 *
	 * @param string|nodeInterface $node        	
	 * @param string|nodeInterface $parent        	
	 * @param unknown $scope        	
	 * @throws \Exception
	 * @return \BaftBase\std\flattenTree\flattenTreeTrait
	 */
	public function setParent($node, $parent, $scope) {

		$nodeInstance = &$this->getNode ( $node, $scope );
		$parent = &$this->getNode ( $parent, $scope );
		
		// change node parent
		$oldParent = $nodeInstance ['parent'];
		if (! empty ( $oldParent )) {
			$oldParent = &$this->getNode ( $oldParent, $scope );
			unset ( $oldParent ['children'] [$nodeInstance->getName ()] );
		}
		
		$nodeInstance ['parent'] = $parent ['instance']->getName ();
		;
		
		$parent ['children'] [$nodeInstance->getName ()] = $nodeInstance->getName ();
		
		return $this;
	
	}

	/**
	 * initial tree with input array config
	 *
	 * @param array $config
	 *        	array (
	 *        	"nodeName"=>array (
	 *        	'instance' => nodeInstanceObject,
	 *        	'parent' => 'string'/nodeInstanceObject,
	 *        	'children' => array ( 'string' , nodeInstanceObject),
	 *        	'property' => array ( any properties)
	 *        	),
	 *        	. . . . . .
	 *        	)
	 * @param string $scope        	
	 */
	public static function fromArray(array $config, $scope) {

		$tree = new self ( $scope );
		
		foreach ( $config as $nodeName => $nodeConf ) {
			
			$nodeObj = $nodeConf ['instance'];
			$parent = $nodeConf ['parent'];
			$children = $nodeConf ['children'];
			$property = $nodeConf ['property'];
			
			$parentName = $parent;
			if ($parent instanceof nodeInterface)
				$parentName = $parent->getName ();
			
			$scopeTree = &$tree->getScope ( $scope );
			
			$scopeTree [$nodeObj->getName ()] = array (
					'instance' => $nodeObj,
					'parent' => $parentName,
					'children' => $nodeConf ['children'],
					'property' => $nodeConf ['property'] 
			);
		}
		return $tree;
	
	}

	/**
	 *
	 * @param string $scope
	 *        	return scope
	 * @param string $parent        	
	 */
	public function toArray($scope) {

		return $this->getScope ( $scope );
	
	}

	/**
	 *
	 * @param unknown $scope        	
	 * @throws \Exception
	 * @return multitype:|boolean
	 */
	public function &isExistScope($scope) {

		$false [] = false;
		if (! isset ( $scope ))
			throw new \Exception ( "requested scope for existancy check can not be null" );
		$this->checkScopeType ( $scope, __FUNCTION__ );
		if (isset ( $this->trees [$scope] ))
			return $this->trees [$scope];
		else
			return $false [0];
	
	}

	/**
	 * return all childe of a scope
	 *
	 * @param string $scope        	
	 * @throws \Exception if scope not set
	 * @return multitype:
	 */
	public function &getScope($scopeName = 'default') {

		$scope = &$this->isExistScope ( $scopeName );
		if ($scope !== false)
			return $scope;
		else
			throw new \Exception ( "requested scope \"$scopeName\" is not exist" );
	
	}

	public function setScope($scope) {

		$this->checkScopeType ( $scope, __FUNCTION__ );
		$this->trees [$scope] = array ();
		return $this;
	
	}

	/**
	 * add custome detail to element of a tree that contain a nodeObject
	 *
	 * @param string $scope        	
	 * @param string|nodeInterface $nodeName        	
	 * @param array $property        	
	 * @return flattenTree
	 */
	public function addProperty($nodeName, $property, $scope = 'default') {

		$node = &$this->getNode ( $nodeName, $scope );
		$newProperties = array_merge ( $property, $node ['property'] );
		$node ['property'] = $newProperties;
		return $this;
	
	}

	/**
	 * get properties of a node
	 *
	 * @param string $scope        	
	 * @param string|nodeInterface $nodeName        	
	 * @return array
	 */
	public function &getProperty($nodeName, $scope = 'default') {

		$node = &$this->getNode ( $nodeName, $scope );
		return $node ['property'];
	
	}

	/**
	 * no return , just change flow of program if node type dose not match
	 *
	 * @param unknown $node        	
	 * @param unknown $clientFunction        	
	 * @throws \Exception
	 */
	private function checkNodeType($node, $clientFunction) {

		if (! $node instanceof nodeInterface && ! is_string ( $node )) {
			$type = gettype ( $node );
			if ($type == "object")
				$type = get_class ( $node );
			throw new \Exception ( "\"$clientFunction\" expect node to be type of String or nodeInterface , but type of \"$type\" given!" );
		}
	
	}

	private function checkScopeType($scope, $clientFunction) {

		if (! is_string ( $scope )) {
			$type = gettype ( $scope );
			if ($type == "object")
				$type = get_class ( $scope );
			throw new \Exception ( "\"$clientFunction\" expect scope name to be string ,but type of \"$type\" is given! " );
		}
	
	}

	/**
	 * if node exist return refrence to it else false
	 *
	 * @param string|nodeInterface $node        	
	 * @param string $scope        	
	 * @return array|boolean
	 */
	public function &isExistNode($node, $scopeName = 'default') {

		$false [] = false;
		
		$this->checkNodeType ( $node, __FUNCTION__ );
		
		$scopeTree = &$this->getScope ( $scopeName );
		$nodeName = $node;
		if ($node instanceof nodeInterface)
			$nodeName = $node->getName ();
		
		if (isset ( $scopeTree [$nodeName] )) {
			return $scopeTree [$nodeName];
		}
		
		return $false [0];
	
	}

	/**
	 * find and return node, false on nothing
	 *
	 * @param string|nodeInterface $node        	
	 * @param string $scopeName        	
	 * @return array|boolean
	 */
	public function &findNode($node, $scopeName = 'default') {

		return $this->isExistNode ( $node, $scopeName );
	
	}

	/**
	 * get a branch(stack of nodes) contain parents of current node (contain current node)
	 * first pop is "first paretn" , last pop is "current node"
	 *
	 * @param
	 *        	string | nodeInterface $node
	 * @param string $scopeName        	
	 * @throws \Exception
	 * @return \Zend\Stdlib\SplStack
	 */
	public function &getNodeParents($node, $scopeName = 'default') {

		$branch = new SplStack ();
		
		if ($node instanceof nodeInterface) {
			$node = $node->getName ();
		}
		$fistNode = $node;
		$parentName = $node;
		
		do {
			$node = $this->isExistNode ( $parentName, $scopeName );
			if ($node)
				$branch [] = $node;
			else
				throw new \Exception ( "broken parents branch for '$fistNode' node. '{$parentName}' node dose not exist in tree ." );
			
			$parentName = $node ['parent'];
		}
		while ( $parentName );
		
		return $branch;
	
	}

	/**
	 * check if parent has this child
	 *
	 * @param string $scopeName        	
	 * @param
	 *        	string | nodeInterface $parent
	 * @param
	 *        	string | nodeInterface $child
	 * @return boolean
	 */
	public function hasChild($scopeName, $parent, $child) {

		$parent = $this->isExistNode ( $parent, $scopeName );
		$child = $this->isExistNode ( $child, $scopeName );
		if (! $parent || ! $child)
			return false;
		
		return array_search ( $child ['instance']->getName (), $parent ['children'] ) !== false;
	
	}

	/**
	 * finding nodes
	 * return list of nodes by customize checking by `$check`
	 * on each child of scope : $check->ckeck(scope,child) , if true => add to list
	 * usefull in case finding nodes by property
	 *
	 * @param string $scope        	
	 * @param checkInterface|callable|method $check
	 *        	must be implement of checkInterface or callabel or any object instance that have method 'check($scope,$nodeProp)'
	 * @throws \Exception
	 * @return multitype:Ambigous <\BaftBase\std\flattenTree\multitype:, multitype:>
	 */
	public function &getNodeBy($scope = 'default', $check) {

		if (! isset ( $scope ))
			throw new \Exception ( "no scope define for requested operation" );
		$scopeNodes = &$this->getScope ( $scope );
		$resultNodes = array ();
		
		if (is_callable ( $check )) {
			$checkObj = new \stdClass ();
			$checkObj->check = $check;
			$check = $checkObj;
		}
		
		if (! is_callable ( [ 
				$check,
				'check' 
		], true ))
			throw new \Exception ( 'method `getNodeBy` expect parameter two to be `checkInterface` or `callable` or an object with method `check` ' );
			
			// create various calling methodology for callable $check input type ,
		$callable = '';
		if ($check instanceof checkInterface)
			$callable = get_class ( $check ) . '::check';
		elseif ($check instanceof \stdClass)
			$callable = $check->check;
		else
			$callable = [ 
					$check,
					'check' 
			];
		
		foreach ( $scopeNodes as $nodeName => $nodeProp ) {
			$result = call_user_func_array ( $callable, [ 
					$nodeName,
					&$nodeProp 
			] );
			if ($result)
				$resultNodes [$nodeName] = &$nodeProp;
		}
		return $resultNodes;
	
	}

	/**
	 * just an alias for getNodeBy
	 *
	 * @param string $scope        	
	 * @param checkInterface $check        	
	 * @return Ambigous <\BaftBase\std\flattenTree\multitype:Ambigous, multitype:unknown >
	 */
	public function &findNodeBy($scope = 'default', $check) {

		return $this->getNodeBy ( $scope, $check );
	
	}

	/**
	 * return a node in a scop
	 *
	 * @param string $scope        	
	 * @param string $name        	
	 * @throws \Exception
	 * @return array
	 */
	public function &getNode($name, $scope = 'default') {

		$node = &$this->isExistNode ( $name, $scope );
		if ($node === false) {
			if (is_object ( $name ))
				$name = $name->getName ();
			throw new \Exception ( "node \"{$name}\" not exist in scope \"{$scope}\"" );
		}
		return $node;
	
	}

	/**
	 * pack conditions tree to a predicateSet Flat
	 *
	 * @param string $statement        	
	 * @return \Zend\Db\Sql\Predicate\PredicateSet
	 */
	public function packNodes($parentNodeName = null, $scope = 'default') {

		$statementConditions = &$this->getScopegetScopes->getTreeScope ( $scope );
		$pack = new PredicateSet ();
		
		$host = &$this;
		$concat = function &(array &$root) use(&$host, $statement, &$concat) {
			$children = $root ['children'];
			if (empty ( $children )) {
				return $root ['instance'];
			}
			
			foreach ( $children as $conditionName ) {
				$childProps = $this->getNode ( $statement, $conditionName );
				
				/**
				 *
				 * @var nodeInterface
				 */
				$childPacked = $concat ( $childProps );
				$root ['instance']->addCondition ( $childPacked, $childProps ['combination'] );
			}
			return $root ['instance'];
		};
		
		foreach ( $statementConditions as $rootName => $rootProp ) {
			if (! isset ( $rootProp ['parent'] ) || empty ( $rootProp ['parent'] )) {
				$rootPacked = $concat ( $statementConditions [$rootName] );
				$pack->addPredicate ( $rootPacked, $rootProp ['combination'] );
			}
		}
		return $pack;
	
	}

	/**
	 * remove a node by scope-name
	 * if customize checking set ($check must be implement of checkInterface)
	 * pass scop and node to $check->ckeck if reurn true then remove it
	 *
	 * @param string $scope        	
	 * @param string|nodeInterface $nodeName        	
	 * @param checkInterface $check        	
	 * @return \BaftBase\std\flattenTree\flattenTree
	 */
	public function removeNode($node, $scope = 'default', checkInterface $check = null) {

		$node = $this->isExistNode ( $node, $scope );
		
		if ($node === false)
			return $this;
		
		$scopeTree = &$this->getScope ( $scope );
		
		$nodeName = $node ['instance']->getName ();
		
		$host = &$this;
		
		$lowLatencyRecursive = function ($nodeName) use(&$lowLatencyRecursive, &$host, &$check, &$scopeTree) {
			$orphanChildren = $scopeTree [$nodeName] ['children'];
			$nodeParent = $scopeTree [$nodeName] ['parent'];
			
			if (isset ( $check ))
				$checkingResult = $check->check ( $scope, $scopeTree [$nodeName] );
			else
				$checkingResult = true;
			
			if ($checkingResult) {
				
				if (! empty ( $nodeParent )) {
					unset ( $scopeTree [$nodeParent] ['children'] [$nodeName] );
				}
				
				if (! empty ( $orphanChildren )) {
					foreach ( $orphanChildren as $orphanName ) {
						$lowLatencyRecursive ( $orphanName );
					}
				}
				unset ( $scopeTree [$nodeName] );
			}
		};
		
		$lowLatencyRecursive ( $nodeName );
		return $this;
	
	}


}

