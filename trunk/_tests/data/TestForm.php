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
	}
	
	public function nameValidate() {
		if (strlen($this->name) < 6) {
			$this->addError('name too short', 'nameError');
		}
	}
}