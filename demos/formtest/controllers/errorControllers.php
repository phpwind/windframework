<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class ErrorControllers extends WindErrorAction {
	public function run() {
		$this->setOutput(array('usernameError' => $this->error->getError('username'),
		                       'passwordError' => $this->error->getError('password')));
		$this->setOutput(L::getInstance('UserForm'), 'userInfo');
		$this->setOutput('show', 'show');
		$this->setTemplate('useForm');
	}
}