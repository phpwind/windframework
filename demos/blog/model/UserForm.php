<?php
/**
 * 用户表单，供登录和注册时验证用
 * 
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class UserForm extends WindEnableValidateModule {
	
	private $username;
	private $password;

	/**
	 * @return 
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return field_type
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
		$this->password = $password ? md5($password) : $password;
	}

	/* (non-PHPdoc)
	 * @see WindEnableValidateModule::validateRules()
	 */
	public function validateRules() {
		return array(
			WindUtility::buildValidateRule("username", "isRequired"), 
			WindUtility::buildValidateRule("password", "isRequired"));
	}

}