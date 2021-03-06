<?php

namespace App\View\Helper;

use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * in view files , read from post , get , route
 *
 * @author root
 *        
 */
class Params extends AbstractHelper {
	protected $request;
	protected $event;

	public function __construct(RequestInterface $request, MvcEvent $event) {

		$this->request = $request;
		$this->event = $event;
	
	}

	public function fromPost($param = null, $default = null) {

		if ($param === null) {
			return $this->request->getPost ( $param, $default )->toArray ();
		}
		
		return $this->request->getPost ( $param, $default );
	
	}

	public function fromRoute($param = null, $default = null) {

		if ($param === null) {
			return $this->event->getRouteMatch ()->getParams ();
		}
		
		return $this->event->getRouteMatch ()->getParam ( $param, $default );
	
	}


}