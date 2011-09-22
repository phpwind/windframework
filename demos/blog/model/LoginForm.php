<?php
class LoginForm extends WindEnableValidateModule {
	
	private $username;
	private $password;
	
	
	/**
	 * @return field_type
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
		$this->password = $password;
	}

	public function validateRules(){
		return array(
			WindUtility::buildValidateRule("username", "isRequired"),
			WindUtility::buildValidateRule("password", "isRequired"),
		);
	}
	
	
	
}