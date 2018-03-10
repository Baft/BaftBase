<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * This autoloading setup is really more complicated than it needs to be for most
 * applications.
 * The added complexity is simply to reduce the time it takes for
 * new developers to be productive with a fresh skeleton. It allows autoloading
 * to be correctly configured, regardless of the installation method and keeps
 * the use of composer completely optional. This setup should work fine for
 * most users, however, feel free to configure autoloading however you'd like.
 */

// Composer autoloading
if (file_exists ( 'vendor/autoload.php' )) {
	$loader = include 'vendor/autoload.php';
}

$zf2Path = false;
$autoloaderConfig = [ 
		'Zend\Loader\StandardAutoloader' => array (
				'namespaces' => array (),
				'autoregister_zf' => true,
				'prefixes' => false,
				'fallback_autoloader' => false 
		) 
];

if (isset ( $applicationConfig ['autoloader'] ) && ! empty ( $applicationConfig ['autoloader'] ))
	$autoloaderConfig = array_replace_recursive ( $autoloaderConfig, $applicationConfig ['autoloader'] );

if (getenv ( 'ZF2_PATH' )) { // Support for ZF2_PATH environment variable or git submodule
	$zf2Path = getenv ( 'ZF2_PATH' );
} elseif (get_cfg_var ( 'zf2_path' )) { // Support for zf2_path directive value
	$zf2Path = get_cfg_var ( 'zf2_path' );
} elseif (is_dir ( VENDOR )) {
	$zf2Path = VENDOR;
}

if ($zf2Path) {
	if (isset ( $loader )) {
		
		$loader->add ( 'Zend', $zf2Path );
		$namespaces = $autoloaderConfig ['Zend\Loader\StandardAutoloader'] ['namespaces'];
		if (! empty ( $namespaces ))
			foreach ( $namespaces as $namespace => $src ) {
				$loader->addpsr4 ( $namespace . NS, $src );
			}
		$classMaps = $autoloaderConfig ['Zend\Loader\ClassMapAutoloader'];
		$loader->addClassMap ( $classMaps );
	} else {
		include $zf2Path . DS . 'Zend' . DS . 'Loader' . DS . 'AutoloaderFactory.php';
		Zend\Loader\AutoloaderFactory::factory ( $autoloaderConfig );
	}
}

if (! class_exists ( 'Zend\Loader\AutoloaderFactory' )) {
	throw new RuntimeException ( 'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.' );
}
