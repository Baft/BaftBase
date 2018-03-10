<?php

namespace BaftBase\view\helper;

use Zend\View\Helper\Url;
use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\ControllerManager;

/**
 * Helper to call actions as view helper (widget)
 */
class ActionHelper extends AbstractHelper implements ServiceLocatorAwareInterface {
	private $controller = null;
	private $action = null;
	private $actionViewModel;
	private $routeParameter = [ ];
	private $postParameter = [ ];
	private $getParameter = [ ];
	/**
	 * initilization parameters used by controller manager to pass to controller constructor on create
	 * @var array
	 */
	private $initParameter = [ ];
	private $viewTemplate = null;

	/**
	 *
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;

	/**
	 * to create (in service factory) an action helper for each action by assigning action name to pre initiated action helper object via view helper service factory function
	 * eg.
	 * 'exampleActionHelper'=> function ($sm){ return new ActionHelper('controllerName','exampleAction',[...]);}
	 *
	 * @param unknown $controller
	 * @param unknown $action
	 * @param array $parameter
	 */
	public function __construct($controller = null, $action = null, $parameter = [], $tempalte = null) {

		$this->setController ( $controller );
		$this->setAction ( $action );
		$this->setRouteParameter ( $parameter );
		$this->setViewTemplate ( $tempalte );

	}

	/**
	 * general to invoke for all action
	 *
	 * @param string $actionNamespace
	 * @param array|Parameters $parameter
	 * @param string|ViewModel $tempalte
	 * @throws \Exception
	 * @return string
	 */
	public function __invoke($controllerName = null, $action = null, $parameter = array(), $tempalte = null) {

		//avoid to reset when initilized in constructor . to make actionWidgets reliable
		if (! isset ( $this->controller ))

			if (! isset ( $controllerName ))
				throw new \Exception ( "controller name can not leave empty on action helper" );

		$this->setController ( $controllerName );

		//avoid to reset when initilized in constructor . to make actionWidgets reliable
		if (! isset ( $this->action )) {

			if (! isset ( $action ))
				throw new \Exception ( "action name can not leave empty on action helper" );

			$this->setAction ( $action );
		}

		if (isset ( $parameter ['post'] )){
			$post=$parameter ['post'];
			unset($parameter ['post']);
			$this->setPostParameter($post);
		}

		if (isset ( $parameter ['query'] )){
			$query=$parameter ['query'];
			unset($parameter ['query']);
			$this->setGetParameter($query);
		}

		if (isset ( $parameter ['init'] )){
			$init=$parameter ['init'];
			unset($parameter ['init']);
			$this->setInitParameter($init);
		}

		if (isset ( $parameter ['route'] )){
			$route=$parameter ['route'];
			unset($parameter ['route']);

			$route['action']=$this->getAction();
			$this->setRouteParameter($route);
		}

		if (isset ( $this->viewTemplate ))
			$this->setViewTemplate ( $tempalte );


		return $this->dispatch();

	}

	/**
	 * render action view model when printing helper
	 */
	public function __toString() {

		return $this->render ();

	}

	private function render() {

		return $this->getView ()->render ( $this->getActionViewModel (), $this->getRouteParameter () );

	}


	private function dispatch(){
		/**
		 *
		 * @var ControllerManager $controllerLoader
		 */
		$controllerLoader = $this->getServiceLocator ()->getServiceLocator ()->get ( 'ControllerLoader' );
		/**
		 *
		 * @var AbstractController $controller
		 */
		$controller= $controllerLoader->get( $this->getController() );

		//$controller->setEvent($this->getServiceLocator ()->getServiceLocator ()->get ( 'Application' )->getMvcEvent()->setController($controller));
		//var_dump($controller->getEvent());

		$post=$controller->getRequest ()->getPost()->fromArray($this->getPostParameter());
		$controller->getRequest ()->setPost(new Parameters($post));

		$query=$controller->getRequest ()->getQuery()->fromArray($this->getGetParameter());
		$controller->getRequest ()->setQuery(new Parameters($query));

		$viewModel = $controller->forward ()->dispatch ( $this->getController (), $this->getRouteParameter () );
		$this->setActionViewModel ( $viewModel );

		if (isset ( $this->viewTemplate )) {
			$this->getActionViewModel ()->setTemplate ( $this->getViewTemplate () );
		}

		return $this->getActionViewModel();
	}

	/**
	 * Set the service locator.
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return AbstractHelper
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {

		$this->serviceLocator = $serviceLocator;
		return $this;

	}

	/**
	 * Get the service locator.
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {

		if (! isset ( $this->serviceLocator ))
			$this->setServiceLocator ( $this->getView ()->getHelperPluginManager () );
		return $this->serviceLocator;

	}

	public function __isset($property) {

		return $this->{$property} == null;

	}

	public function getActionViewModel() {

		return $this->actionViewModel;

	}

	public function setActionViewModel($actionViewModel) {

		$this->actionViewModel = $actionViewModel;
		return $this;

	}

	public function setViewTemplate($ViewTemplate) {

		$this->viewTemplate = $ViewTemplate;
		return $this;

	}

	public function getViewTemplate() {

		return $this->viewTemplate;

	}

	public function setController($controller) {

		$this->controller = $controller;
		return $this;

	}

	public function getController() {

		return $this->controller;

	}

	public function setAction($action) {

		$this->action = $action;
		return $this;

	}

	public function getAction() {

		return $this->action;

	}

	/**
	 *
	 * @return the $routeParameter
	 */
	public function getRouteParameter() {

		return $this->routeParameter;

	}


	/**
	 *
	 * @return the $postParameter
	 */
	public function getPostParameter() {

		return $this->postParameter;

	}


	/**
	 *
	 * @return the $getParameter
	 */
	public function getGetParameter() {

		return $this->getParameter;

	}


	/**
	 *
	 * @param multitype: $routeParameter
	 */
	public function setRouteParameter($routeParameter) {

		$this->routeParameter = $routeParameter;

	}


	/**
	 *
	 * @param multitype: $postParameter
	 */
	public function setPostParameter($postParameter) {

		$this->postParameter = $postParameter;

	}


	/**
	 *
	 * @param multitype: $getParameter
	 */
	public function setGetParameter($getParameter) {

		$this->getParameter = $getParameter;

	}
	/**
	 * @return the $initParameter
	 */
	public function getInitParameter() {

		return $this->initParameter;
	}



	/**
	 * @param multitype: $initParameter
	 */
	public function setInitParameter($initParameter) {

		$this->initParameter = $initParameter;
	}





}
