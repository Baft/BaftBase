<?php

namespace ZfcUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController {
	
	public function createPartialAction() {
	//@TODO wysiwyg to write html/css code and save as static partial . like footer , headers
	//@TODO use placeholder/ob_start/ZendPhprendere to render partials : at least inject variables(viewModel) to partials at most render php code on partials
	//@TODO lazy load partial to Zend template locator to be able load theme.
	}
	
	
	
	
	function createPageAction() {
		//@TODO create page route type
		//@TODO 
	}
}