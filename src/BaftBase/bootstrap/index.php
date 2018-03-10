<?php
// use \baft\autoload;
/**
 * This makes our life easier when dealing with paths.
 *
 * Everything is relative to the application root now.
 */
// declare(encoding='UTF-8');

/* ################################################ */
/* ################## CONSTANTS ################# */
/* ################################################ */
date_default_timezone_set ( 'Asia/Tehran' );

defined ( "NS" ) || define ( 'NS', "\\" );
defined ( "DS" ) || define ( 'DS', DIRECTORY_SEPARATOR );
defined ( "PHP" ) || define ( "PHP", ".php" );

defined ( "ROOT" ) || define ( 'ROOT', dirname ( __DIR__ ) );
defined ( "APP_ROOT" ) || define ( 'APP_ROOT', ROOT . DS . 'module' );
defined ( "VENDOR" ) || define ( "VENDOR", ROOT . DS . "vendor" );
defined ( "APP_PUBLIC" ) || define ( "APP_PUBLIC", ROOT . DS . "public" );
defined ( "CORE" ) || define ( 'CORE', VENDOR . DS . 'baft' );

/* ################################################ */
/* ################## initialize ################# */
/* ################################################ */

// @TODO do this one time , not on each request
$new_include_path = VENDOR . DS . PATH_SEPARATOR . APP_ROOT . DS . PATH_SEPARATOR . VENDOR . DS . CORE . DS . PATH_SEPARATOR;
$old_include_path = get_include_path ();
set_include_path ( $new_include_path . PATH_SEPARATOR . $old_include_path );

chdir ( dirname ( __DIR__ ) );

// include_once APP_ROOT.DS."config".DS."application.php";
include_once (APP_ROOT . DS . 'config' . DS . 'config' . PHP);
include_once (CORE . DS . 'initAutoload' . PHP);

// save content of config file to a local variable (write on fly , its php core behavior)
$global = $global;

baft\initAutoload::initLoader ( $global ["autoloader"] );
$applicationName = baft\initAutoload::getApplication ();
$environmentName = baft\initAutoload::getEnvironment ();

/* ################################################ */
/* ################## runner ################# */
/* ################################################ */

$core = \baft\mvc\applicationDecorator::init ( $applicationName, $environmentName, $global );
$core->bootstrap ();
$core->run ();
