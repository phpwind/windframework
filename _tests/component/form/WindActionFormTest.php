<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
include('core/base/WindModule.php');
include('core/WindMessage.php');
include('core/WindErrorMessage.php');
include('component/form/WindActionForm.php');

class UserForm extends WindActionForm {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;
	public function __construct() {
		parent::__construct();
		$this->setErrorAction('error');
	}
	public function nameValidate() {
		if (strlen($this->name) < 6) {
			$this->addError('name too short', 'nameError');
		}
	}
}
class WindActionFormTest extends BaseTestCase {
	private $obj;
	public function setUp() {
		$this->obj = new UserForm();
	}
	public function tearDown() {

	}
	public function testGetIsValidation() {
		$this->assertTrue($this->obj->getIsValidation());
	}
	public function testSetProperties() {
		$array = array('name' => 'phpwind', 'password' => 'phpwind.net', 'address' => 'china', '_isValidate' => true, 
			'site' => 'www.phpwind.net');
		$this->obj->setProperties($array);
		$this->assertTrue($this->obj->getIsValidation());
		$this->assertEquals('phpwind', $this->obj->name);
		$this->assertEquals('phpwind.net', $this->obj->password);
		$this->assertEquals('china', $this->obj->address);
		$this->assertEquals('', $this->obj->site);
	}
	
	public function testError() {
		$array = array('name' => 'php', 'password' => 'phpwind.net', 'address' => 'china', '_isValidate' => true, 
			'site' => 'www.phpwind.net');
		$this->obj->setProperties($array);
		$this->obj->validation();
		$error = WindErrorMessage::getInstance();
		$this->assertEquals('name too short', $error->getError('nameError'));
	}
}

