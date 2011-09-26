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
	 * @return string 返回用户名
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return string 返回用户密码
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param string $password
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