<?php

namespace baft\loader;

define ( "ZEND_LOADER_PATH", VENDOR . DS . 'Zend' . DS . 'Loader' );
include_once (ZEND_LOADER_PATH . DS . 'StandardAutoloader' . PHP);
use Zend\Loader\StandardAutoloader;

/**
 * override zend StandardAutoloader to avoid replacing "_" in class names
 *
 * @author root
 *        
 */
class baftStandardAutoloader extends StandardAutoloader {

	public function __construct($options = null) {

		parent::__construct ( $options );
	
	}

	/**
	 * Register a namespace/directory pair
	 *
	 * @param string $namespace        	
	 * @param string $directory        	
	 * @return StandardAutoloader
	 */
	public function registerNamespace($namespace, $directory) {

		$namespace = rtrim ( $namespace, self::NS_SEPARATOR ) . self::NS_SEPARATOR;
		if (is_array ( $directory )) {
			if (! isset ( $directory ['path'] )) {
				require_once ZEND_LOADER_PATH . '/Exception/InvalidArgumentException.php';
				throw new Exception\InvalidArgumentException ();
			}
			$directory = $directory ['path'];
		}
		$this->namespaces [$namespace] = $this->normalizeDirectory ( $directory );
		return $this;
	
	}

	/**
	 * Transform the class name to a filename
	 *
	 * @param string $class        	
	 * @param string $directory        	
	 * @param boolean $prefix_separator        	
	 * @return string
	 */
	protected function transformClassNameToFilename($class, $directory, $prefix_separator = true) {
		
		// $class may contain a namespace portion, in which case we need
		// to preserve any underscores in that portion.
		$matches = array ();
		preg_match ( '/(?P<namespace>.+\\\)?(?P<class>[^\\\]+$)/', $class, $matches );
		
		$class = (isset ( $matches ['class'] )) ? $matches ['class'] : '';
		$namespace = (isset ( $matches ['namespace'] )) ? $matches ['namespace'] : '';
		$filePath = '';
		
		$filePath .= $directory;
		$filePath .= str_replace ( self::NS_SEPARATOR, '/', $namespace );
		if ($prefix_separator)
			$filePath .= str_replace ( self::PREFIX_SEPARATOR, '/', $class );
		else
			$filePath .= $class;
		$filePath .= '.php';
		return $filePath;
	
	}

	/**
	 * Load a class, based on its type (namespaced or prefixed)
	 *
	 * @param string $class        	
	 * @param string $type        	
	 * @return bool string
	 * @throws Exception\InvalidArgumentException
	 */
	protected function loadClass($class, $type) {

		if (! in_array ( $type, array (
				self::LOAD_NS,
				self::LOAD_PREFIX,
				self::ACT_AS_FALLBACK 
		) )) {
			require_once ZEND_LOADER_PATH . '/Exception/InvalidArgumentException.php';
			throw new Exception\InvalidArgumentException ();
		}
		
		// Fallback autoloading
		if ($type === self::ACT_AS_FALLBACK) {
			// create filename
			$filename = $this->transformClassNameToFilename ( $class, '' );
			$resolvedName = stream_resolve_include_path ( $filename );
			if ($resolvedName !== false) {
				return include $resolvedName;
			}
			return false;
		}
		
		// Namespace and/or prefix autoloading
		foreach ( $this->$type as $leader => $path ) {
			$prefix_separate = null;
			if (0 === strpos ( $class, $leader )) {
				// Trim off leader (namespace or prefix)
				$trimmedClass = substr ( $class, strlen ( $leader ) );
				
				if (is_array ( $path )) {
					if (isset ( $path ['prefix_separate'] ))
						$prefix_separate = $path ['prefix_separate'];
					$path = $path ['path'];
				}
				// create filename
				$filename = $this->transformClassNameToFilename ( $trimmedClass, $path, $prefix_separate );
				if (file_exists ( $filename )) {
					return include $filename;
				}
				return false;
			}
		}
		return false;
	
	}


}
