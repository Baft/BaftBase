<?php

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Session as session;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router as router;
use Zend\Http\Request;
use Zend\Session\Container;
use Zend\Form\Form;

/**
 * Post Request Get (PRG) form
 * make form to recive form data from post and response on get
 *
 * @author web
 *        
 */
class prgForm extends AbstractPlugin implements ServiceManagerAwareInterface, ListenerAggregateInterface {
	
	/**
	 *
	 * @var ServiceManager
	 */
	protected $serviceManager;

	/**
	 *
	 * @param string $prgUrl        	
	 * @param Zend\Form\Form|array $form        	
	 * @param array $redirections
	 *        	redirection urls ['form_invalid'=>'' , 'invalid_request'=>'' , 'form_valid'=>'']
	 * @param array $callback
	 *        	callbacks in each considtions ['form_invalid'=>callback1 , 'invalid_request'=>callback2 , 'form_valid'=>callback3]
	 */
	public function __construct($prgUrl, $form, $redirections, $callbacks) {

		var_dump ( 'fasdfds' );
	
	}

	public function setServiceManager(ServiceManager $serviceManager) {

		$this->serviceManager = $serviceManager;
	
	}

	public function getServiceManager() {

		return $this->serviceManager;
	
	}


}
