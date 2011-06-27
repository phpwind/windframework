<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/form/WindActionForm.php');
class TestForm extends WindActionForm {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;

	public function __construct() {
		$this->setIsValidation(true);
		$this->setErrorAction('class', 'showError');
	}
	
	public function nameValidate() {
		if (strlen($this->name) < 6) {
			$this->addError('name too short', 'nameError');
		}
	}
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @return the $address
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @return the $nick
	 */
	public function getNick() {
		return $this->nick;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param field_type $address
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	/**
	 * @param field_type $nick
	 */
	public function setNick($nick) {
		$this->nick = $nick;
	}
}