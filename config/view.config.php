<?php
return [
		'view_helpers' => array (
				'invokables' => array (
						'spaurl' => 'BaftBase\view\helper\spaUrl',
						'exceptionTraceString' => 'BaftBase\View\helper\exceptionTraceString',
						'pageTitle' => 'BaftBase\View\Helper\PageTitleHelper',
						'baftBaseActionWidget' => 'BaftBase\View\Helper\ActionHelper',
				),
				'aliases' => array(
						'action'=>'baftBaseActionWidget',
						'actionWidget'=>'baftBaseActionWidget',
						'actionwidget'=>'baftBaseActionWidget',
						'actionhelper'=>'baftBaseActionWidget',
						'actionHelper'=>'baftBaseActionWidget',
				)
		),
		'view_helper_config' => array (

		)
];