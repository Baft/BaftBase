<?php

namespace BaftBase\controller\plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\I18n\Translator\TranslatorServiceFactory;
use Zend\I18n\Translator\Translator as orginTranslator;

class translatorControllerPlugin extends AbstractPlugin {

	private $translator;

	public function __construct(orginTranslator $translator) {

		if (! isset ( $this->translator ))
			$this->translator = $translator;

	}

	public function __invoke($message, $textDomain = 'default', $locale = null) {

		return $this->translator->translate ( $message, $textDomain, $locale );

	}

	public function __call($method, $args) {
		// here we need an array at first param.
		return call_user_func_array ( array (
				$this->translator,
				$method
		), $args );

	}

	// @FIXME need static variable in this function
	// public static function __callstatic($method, $args)
	// {
	// // here we need an array at first param.
	// return call_user_func_array(array(self::translator, $method), $args);
	// }
}