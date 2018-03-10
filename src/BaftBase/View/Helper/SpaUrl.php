<?php

namespace BaftBase\view\helper;

use Zend\View\Helper\Url;
use Zend\View\Helper\AbstractHelper;

/**
 * Helper for making easy links and getting urls that depend on the routes and router.
 */
class SpaUrl extends AbstractHelper {

	/**
	 * Generates a url given the name of a route.
	 *
	 * @see Zend\Mvc\Router\RouteInterface::assemble()
	 * @param string $name
	 *        	Name of the route
	 * @param array $params
	 *        	Parameters for the link
	 * @param array|Traversable $options
	 *        	Options for the route
	 * @param bool $reuseMatchedParams
	 *        	Whether to reuse matched parameters
	 * @return string Url For the link href attribute
	 * @throws Exception\RuntimeException If no RouteStackInterface was provided
	 * @throws Exception\RuntimeException If no RouteMatch was provided
	 * @throws Exception\RuntimeException If RouteMatch didn't contain a matched route name
	 * @throws Exception\InvalidArgumentException If the params object was not an array or \Traversable object
	 */
	public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = false) {
		$urlHelper=$this->getView()->plugin('url');
		$urlString = $urlHelper( $name, $params, $options, $reuseMatchedParams );

		$newUrlString='';
		$urlParts = [ ];
		if (!preg_match ( "/(?P<route>^\/[^\\?#]+)?(?P<query>\\?[^\\?#]+)?(?P<hash>#[^#]+)?/", $urlString, $urlParts ))
			throw new \Exception("can not parse url");

		if(isset($urlParts['query']))
			$newUrlString.=$urlParts['query'];
		if(isset($urlParts['route']))
			$newUrlString.="#".$urlParts['route'];

		return $newUrlString;
	}

}
