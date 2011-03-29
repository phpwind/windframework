<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.utility.WindUtility');
class NormalFormController extends WindController {

	public function run() {
		$this->setOutput(array('title' => '通用的验证方式'));
		$this->setTemplate('NormalForm');
	}

	public function postAction() {
		$this->setOutput(array('title' => '显示用户输入的表单数据'));
		$info = $this->getInput('inputData');
		L::import('controllers.actionForm.UserForm');
		$userInfo = new UserForm();
		$userInfo->setUsername($info->username);
		$userInfo->setPassword($info->password);
		$this->setOutput(array('notice' => '你没有使用userForm', 'userInfo' => $userInfo));
		$this->setTemplate('showInput');
	}

	public function validatorFormRule($action) {
		$rules = array();
		$rules['post'][] = WindUtility::buildValidateRule('username', 'isRequired');
		$rules['post'][] = WindUtility::buildValidateRule('password', 'isLegalLength', array(6), 123456, '用户密码小于6位，重设为123456!');
		return isset($rules[$action]) ? $rules[$action] : array();
	}
}