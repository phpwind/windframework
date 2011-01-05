<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
include('core/base/WindModule.php');
include('component/form/WindActionForm.php');

class UserForm extends WindActionForm {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;
	public function __construct() {
		$this->setErrorAction('error', 'showError');
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
		$array = array('name' => 'php', 'password' => 'phpwind.net', 'address' => 'china', '_isValidate' => true, 
			'site' => 'www.phpwind.net');
		$this->obj->setProperties($array);
		$this->assertTrue($this->obj->getIsValidation());
		$this->assertEquals('php', $this->obj->name);
		$this->assertEquals('phpwind.net', $this->obj->password);
		$this->assertEquals('china', $this->obj->address);
		$this->assertEquals('', $this->obj->site);
	}
	
	public function testValidationAndGetError() {
		$error = $this->obj->getError();
		$this->assertTrue(is_array($error) && count($error) == 0);
		$this->obj->validation();
		$error = $this->obj->getError();
		$this->assertTrue(is_array($error) && ('name too short' == $error['nameError']) && count($error) == 1);
	}
	public function testAddError() {
		$this->assertFalse($this->obj->addError(''));
		$array = array('name' => 'php', 'password' => 'phpwind.net', 'address' => 'china', '_isValidate' => true, 
			'site' => 'www.phpwind.net');
		$this->obj->addError($array);
		$error = $this->obj->getError();
		$this->assertTrue(is_array($error) && count($error) == 5);
		$this->assertTrue('php' == $error['name'] && 'phpwind.net' == $error['password']);
		$this->obj->addError('ppp');
		$this->assertEquals('ppp', $this->obj->getError(0));
	}
	
	public function testGetErrorAction() {
		list($action, $actionClass) = $this->obj->getErrorAction();
		$this->assertTrue($action == 'showError' && $actionClass = 'error');
		$this->obj->setErrorAction('controller.ErrorController');
		list($action, $actionClass) = $this->obj->getErrorAction();
		$this->assertTrue($actionClass == 'controller.ErrorController' && $action = 'run');
	}
	public function testSetIsValidation() {
		$this->obj->setIsValidation(false);
		$this->assertFalse($this->obj->getIsValidation());
	}
}

