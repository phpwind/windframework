<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class FormController extends WindController {
	public function run() {
		$this->setOutput(array('title' => '用户输入表单自动获得UserForm对象'));
		$this->setTemplate('Form');
	}
	
	/**
	 * 获得数据
	 */
	public function postForm() {
		$this->setOutput(array('title' => '显示用户输入的表单数据'));
		$userInfo = L::getInstance('UserForm');
		$this->setOutput(array('userInfo' => $userInfo));
		$this->setTemplate('showInput');
	}
}