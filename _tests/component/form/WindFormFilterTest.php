<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.filter.WindFormFilter');
L::import('WIND:core.WindHttpRequest');
L::import('WIND:core.WindHttpResponse');
L::import('WIND:core.WindActionForm');

class UserFormTest extends WindActionForm {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;
	protected $_isValidate = false;
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

class WindFormFilterTest extends BaseTestCase {
	private $obj;
	private $request;
	private $response;
	public function setUp() {
		$this->obj = new WindFormFilter();
	}
	public function tearDown() {
		$this->obj = null;
		$this->request = null;
		$this->response = null;
	}
	public function testNullForm() {
		$p = $this->obj->doBeforeProcess(WindHttpRequest::getInstance(), WindHttpResponse::getInstance());
		$this->assertEquals(null, $p);
	}
	
	public function testSetFormNoValidation() {
		$_GET['name'] = 'phpwind';
		$_GET['formName'] = 'UserFormTest';
		throw new PHPUnit_Framework_IncompleteTestError('No complete');
		$response = WindHttpResponse::getInstance();
		$response->setDispatcher(WindDispatcher::getInstance());
		$this->obj->doBeforeProcess(WindHttpRequest::getInstance(), $response);
		$userForm = L::getInstance('UserFormTest');
		$this->assertEquals('phpwind', $userForm->name);
	}
	
	/**
	 * sendError留待WindErrorMessage测试
	 */
	public function testSetFormWithValidation() {
		$_GET['name'] = 'wind';
		$_GET['formName'] = 'UserFormTest';
		$_GET['_isValidate'] = true;
		throw new PHPUnit_Framework_IncompleteTestError('No complete');
		/*try {
			$response = WindHttpResponse::getInstance();
			$response->setDispatcher(WindDispatcher::getInstance());
			$this->obj->doBeforeProcess(WindHttpRequest::getInstance(), $response);
		} catch (exception $e) {
			
		}
		$this->assertEquals('name too short', WindErrorMessage::getError('nameError'));
		$userForm = L::getInstance('UserForm');
		$this->assertEquals('wind', $userForm->name);*/
	}
}

