<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class MemberForm extends WindEnableValidateModule {

	private $name = 'xxx';

	private $sex;

	private $age;

	private $email;
	
	private $rules = array();
	
	
	public function __construct() {
		$this->rules[] = $this->buildValidateRule('name', 'isLegalLength', 5, 'xxx', 'ErrorNameLength');
		$this->rules[] = $this->buildValidateRule('email', 'isEmail', array(), '', 'ErrorEmail!');
		$this->rules[] = $this->buildValidateRule('sex', 'isLegalLength', array(), '1', 'ErrorSexType');
		$this->rules[] = $this->buildValidateRule('age', 'isInt', array(), '20', 'ErrorAge');
	}

	/**
	 * 返回验证规则
	 * 
	 * validator : required/not-required
	 * @return multitype:multitype:string  
	 */
	protected function validateRules() {
		return $this->rules;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $sex
	 */
	public function getSex() {
		return $this->sex;
	}

	/**
	 * @return the $age
	 */
	public function getAge() {
		return $this->age;
	}

	/**
	 * @return the $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $sex
	 */
	public function setSex($sex) {
		$this->sex = $sex;
	}

	/**
	 * @param field_type $age
	 */
	public function setAge($age) {
		$this->age = $age;
	}

	/**
	 * @param field_type $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}
}