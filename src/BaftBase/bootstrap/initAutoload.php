<?php

namespace baft;

class initAutoload {

	private static function getLoader($lib_path, $config) {

		if (! is_array ( $config ) || empty ( $config ))
			$config = null;
		if ($lib_path) {
			$loader_path = $lib_path . 'Loader' . DS . 'AutoloaderFactory' . PHP;
			include_once ($loader_path);
			\Zend\Loader\AutoloaderFactory::factory ( $config );
		}
	
	}

	public static function getApplication() {

		/*
		 * ENV set by environment variable
		 * or by putenv() | apache_setenv() -> only exist request duration
		 */
		$applicationName = getenv ( 'APPLICATION' );
		if (! $applicationName) {
			if (isset ( $_ENV ['APPLICATION'] )) {
				$applicationName = $_ENV ['APPLICATION'];
				defined ( "APPLICATION" ) || define ( 'APPLICATION', $applicationName );
			}
		}
		
		defined ( "APPLICATION" ) || define ( 'APPLICATION', 'Baft' );
		
		return APPLICATION;
	
	}

	public static function getEnvironment() {

		/*
		 * ENV set by environment variable
		 * or by putenv() | apache_setenv() -> only exist request duration
		 */
		$environment = getenv ( 'ENVIRONMENT' );
		if (! $environment) {
			if (isset ( $_ENV ['ENVIRONMENT'] )) {
				$environment = $_ENV ['ENVIRONMENT'];
				defined ( "ENVIRONMENT" ) || define ( 'ENVIRONMENT', $environment );
			}
		}
		
		// @TODO some default environment have to exist ('production' and 'development' exist by default in system , and 'production' selected default), then each application customize ENV`s
		defined ( "ENVIRONMENT" ) || define ( 'ENVIRONMENT', 'development' );
		
		if (stcasecmp ( ENVIRONMENT, 'development' ) == 0) {
			ini_set ( "display_errors", "On" );
			error_reporting ( E_ALL );
		}
		
		return ENVIRONMENT;
	
	}

	public static function initLoader($config = null) {

		$zend_path = false;
		$loader_object = false;
		if (getenv ( 'ZF2_PATH' )) { // Support for ZF2_PATH environment variable or git submodule
			$zend_path = getenv ( 'ZF2_PATH' );
			$loader_object = static::getLoader ( $zend_path, $config );
		} elseif (get_cfg_var ( 'zf2_path' )) { // Support for zf2_path directive value
			$zend_path = get_cfg_var ( 'zf2_path' );
			$loader_object = static::getLoader ( $zend_path, $config );
		} elseif (is_dir ( VENDOR . DS . 'Zend' )) {
			$zend_path = VENDOR . DS . 'Zend' . DS;
			$loader_object = static::getLoader ( $zend_path, $config );
		}
	
	}

 // init_loader
}//CLASS