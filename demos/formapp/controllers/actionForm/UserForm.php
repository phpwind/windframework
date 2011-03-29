<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindEnableValidateModule');
L::import('WIND:component.utility.WindUtility');

class UserForm extends WindEnableValidateModule {

	/**
	 *设置错误处理controller
	 *
	 * @var string
	 */
	protected $errorControler = 'controllers.ErrorController';

	private $username;

	private $password;

	/**
	 * (non-PHPdoc)
	 * @see WindEnableValidateModule::validateRules()
	 */
	public function validateRules() {
		$rules = array();
		$rules[] = WindUtility::buildValidateRule('username', 'isRequired');
		$rules[] = WindUtility::buildValidateRule('password', 'isLegalLength', array(6), 123456, '用户密码小于6位，重设为123456!');
		return $rules;
	}

	/**
	 * @return the $username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param field_type $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param field_type $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
}