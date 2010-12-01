<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('actionControllers.actionForm.MemberForm');

class FormController extends WindController {
	public function run() {
		$userForm = L::getInstance('UserForm');
		print_r($userForm);
		echo $userForm;
		echo "mmmmm";
	}
}