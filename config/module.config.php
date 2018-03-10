<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the
 *       canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc.
 *            (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
namespace BaftBase;

use Zend\I18n\Translator\TranslatorServiceFactory;
use BaftBase\Controller\Plugin\BaftFlashMessenger;
use Zend\ServiceManager\Factory\InvokableFactory;

return array (


		'controller_plugins' => array (
				'invokables' => array (
						'authn' => 'Application\Controller\Plugin\authn',
						'log' => 'Application\Controller\Plugin\log'
				),
				'factories' => array (
						\Zend\Mvc\Controller\Plugin\FlashMessenger::class => function ($sm) {

// 							$serviceLocator = $sm->getController ()->getServiceLocator ();

							return new BaftFlashMessenger ();
						},
						'zendmvccontrollerpluginflashmessenger' => InvokableFactory::class,
						'translator' => function ($sm) {

							$serviceLocator = $sm->getController ()->getServiceLocator ();
							$serviceFactory = new TranslatorServiceFactory ();
							$translator = $serviceFactory->createService ( $serviceLocator );

							return new \BaftBase\Controller\Plugin\TranslatorControllerPlugin ( $translator );
						}
				)
		)
);
