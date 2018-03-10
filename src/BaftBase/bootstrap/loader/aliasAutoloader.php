<?php

namespace baft\loader;

use Zend\Loader\SplAutoloader, Zend\Loader\ShortNameLocator;
use Traversable;

/**
 * enable autoloader to create alias for class
 * aliases can use to map a nonExistance class to real class
 * ex.
 * new \noRealPath\noRealClass() -> load: realPath\RealClass
 * "autoloader"=>array(
 * 'baft\loader\aliasAutoloader'=>array(
 * 'aliasPath\aliasClass' => array(
 * 'file_path' => CORE . DS . 'testInterface' . PHP, // real path of file
 * 'namespace' => 'realPath/realClass' // real namespace
 * )
 * )
 * )
 *
 * @author root
 *        
 */
class aliasAutoloader implements SplAutoloader, ShortNameLocator {
	const ALIAS_FILE_PATH = 'file_path';
	const ALIAS_NAMESPACE = 'namespace';
	protected static $option;
	public static $registered_alias = array ();

	/*
	 * (non-PHPdoc) @see \Zend\Loader\SplAutoloader::__construct()
	 */
	public function __construct($options = null) {

		if (is_array ( $options ) && ! empty ( $options ))
			$this->setOptions ( $options );
	
	}

	/*
	 * (non-PHPdoc) @see \Zend\Loader\SplAutoloader::setOptions()
	 */
	public function setOptions($options) {

		$this->registerAlias ( $options );
	
	}

	public function isLoaded($name) {

	}

	/**
	 * Return full class name for a named helper
	 *
	 * @param string $name        	
	 * @return string
	 */
	public function getClassName($name) {

	}

	/**
	 * Load a helper via the name provided
	 *
	 * @param string $name        	
	 * @return string
	 */
	public function load($name) {

	}

	/*
	 * (non-PHPdoc) @see \Zend\Loader\SplAutoloader::autoload()
	 */
	public function autoload($class) {

		if (! empty ( $this->option ))
			// search in class aliases
			foreach ( $this->option as $alias => $aliasDefenition )
				if (strcasecmp ( $class, $alias ) == 0) {
					$filePath = trim ( $aliasDefenition [static::ALIAS_FILE_PATH] );
					if (! empty ( $filePath ))
						require_once ($aliasDefenition [static::ALIAS_FILE_PATH]);
					$this->createAlias ( $class, $aliasDefenition [static::ALIAS_NAMESPACE] );
				}
	
	}

	public static function registerAlias($options) {

		foreach ( $options as $alias => $aliasConfig ) {
			if (! isset ( $aliasConfig [static::ALIAS_FILE_PATH] ))
				throw new \Zend\Loader\Exception\InvalidArgumentException ( "path of alias : \"{$alias}\" not defined", '' );
			
			if (! isset ( $aliasConfig [static::ALIAS_NAMESPACE] ))
				throw new \Zend\Loader\Exception\InvalidArgumentException ( "real name of alias : \"{$alias}\" not defined", '' );
			
			self::$option [$alias] = $aliasConfig;
		}
	
	}

	public static function createAlias($alias, $real) {

		$registeredAliases = static::$registered_alias;
		if (! empty ( $registeredAliases )) {
			foreach ( $registeredAliases as $registeredAlias ) {
				if (strcasecmp ( md5 ( $registeredAlias ), md5 ( $alias ) ) == 0) {
					return;
				}
			}
		}
		static::$registered_alias [] = $alias;
		class_alias ( $real, $alias );
	
	}

	/*
	 * (non-PHPdoc) @see \Zend\Loader\SplAutoloader::register()
	 */
	public function register() {

		spl_autoload_register ( array (
				$this,
				'autoload' 
		), true, true );
	
	}


}