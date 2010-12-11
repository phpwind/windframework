<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class IndexController extends WindController {
	public function run() {
		
	}
	
	public function iforward() {
		$this->forwardRedirectAction('run', 'otherControllers.ForwardController', array('p' => '22'));
	}
}