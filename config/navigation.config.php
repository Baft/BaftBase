<?php
return [
		'navigation' => array (
				'default' => array (
						array (
								'label' => 'Dashboard',
								'route' => 'main',
								"icon" => "icon-home",
						),
						array (
								'label' => 'تجهیزات',
								"class" => "accordion-toggle  collapsed",
								"data_toggle" => "collapse",
								"uri" => "#basic-tab",
								"id" => "basic-tab",
								"icon" => "icon-settings",
								'pages' => array (
										array (
												'label' => 'IP Configuration',
												'route' => 'main/sub',
												"icon" => "icon-plus",
												'action' => "ipconfig",
												"controller" => "Basic"
										),
										array (
												'label' => 'Administration',
												"icon" => "icon-user",
												'route' => 'main/sub',
												// 'resource' => 'administration',
												'action' => "administration",
												"controller" => "Basic"
										),
										array ( // http://waf/Basic/webfirewalllog
												'label' => 'Firewall Log',
												"icon" => "icon-fire",
												'route' => 'main/sub',
												// 'resource' => 'Basic',
												'action' => "webfirewalllog",
												"controller" => "Basic"
										)
								)
						),
						array (
								'label' => 'Security',
								"class" => "accordion-toggle  collapsed",
								"data_toggle" => "collapse",
								"uri" => "#security-tab",
								"id" => "security-tab",
								"icon" => "icon-lock icon-2x",
								// 'resource' => 'security',
								'pages' => array (
										array ( // http://waf/Security/profiles
												'label' => 'Policy Manager',
												"icon" => "icon-list",
												'route' => 'main/sub',
												'action' => "profiles",
												// 'resource' => 'profiles',
												"controller" => "Security",
												"pages" => array (
														array ( // http://waf/Security/profiles
																'label' => 'Add/Edit Security Policy',
																"is_menu_item" => false,
																'route' => 'main/sub',
																'action' => "profile",
																// 'resource' => 'profiles',
																"controller" => "Security"
														)
												)
										),

										array ( // http://waf/Security/parameterprotection
												'label' => 'Parameter Protection',
												'route' => 'main/sub',
												"icon" => "icon-eye-open",
												'action' => "parameterprotection",
												"controller" => "Security"
										),
										array ( // http://waf/Security/cloaking
												'label' => 'Cloaking',
												"icon" => "icon-indent-left",
												'route' => 'main/sub',
												'action' => "cloaking",
												"controller" => "Security"
										),
										array ( // http://waf/Security/requestlimits
												'label' => 'Request Limits',
												'route' => 'main/sub',
												'action' => "requestlimits",
												"icon" => "icon-edit",
												"controller" => "Security"
										),
										array ( // http://waf/Security/urlprotection
												'label' => 'URL Protection',
												"icon" => "icon-road",
												'route' => 'main/sub',
												'action' => "urlprotection",
												"controller" => "Security"
										)
								)
						),
						array (
								'label' => 'Web Sites',
								"class" => "accordion-toggle  collapsed",
								"data_toggle" => "collapse",
								"uri" => "#website-tab",
								"icon" => "icon-globe icon-2x",
								"id" => "website-tab",
								'pages' => array (
										array ( // http://waf/Security/services
												'label' => 'Services',
												'route' => 'main/sub',
												"icon" => "icon-cog",
												'action' => "services",
												"controller" => "Security",
												"pages" => array (
														array ( // http://waf/Security/profiles
																'label' => 'Add/Edit Service',
																"is_menu_item" => false,
																'route' => 'main/sub',
																'action' => "service",
																// 'resource' => 'profiles',
																"controller" => "Security"
														)
												)
										),
										array (
												'label' => 'Trusted Host Management',
												'route' => 'main/sub',
												'action' => "trustedhosts",
												"controller" => "Basic",
												"icon" => "icon-thumbs-up",
												"pages" => array (
														array (
																'label' => 'add/edit Trusted Group',
																'route' => 'main/sub',
																"is_menu_item" => false,
																'action' => "trustedgroup",
																"controller" => "Basic"
														),
														array (
																'label' => 'add/edit Trusted Host',
																'route' => 'main/sub',
																"is_menu_item" => false,
																'action' => "trustedhost",
																"controller" => "Basic"
														)
												)
										)
								)
						)
				)
		)//navigation
];