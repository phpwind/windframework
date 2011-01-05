<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class NormalFormController extends WindController {
	public function run() {
		$this->setOutput(array('title' => '用户输入表单安装正常的方式获得数据'));
		$this->setTemplate('NormalForm');
	}
	
	public function postForm() {
		$this->setOutput(array('title' => '显示用户输入的表单数据'));
		L::import('controllers.actionForm.UserForm');
		$userInfo = new UserForm();
		$userInfo->setProperties($this->getInput(array('username', 'password')));
		$this->setOutput(array('notice' => '你没有使用userForm',
								'userInfo' => $userInfo));
		$this->setTemplate('showInput');
	}
}