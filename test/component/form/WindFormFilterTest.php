<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import(R_P . '/test/component/form/WindActionFormTest.php');
L::import(WIND_PATH . '/component/form/WindFormFilter.php');

L::import(WIND_PATH . '/component/request/WindHttpRequest.php');
L::import(WIND_PATH . '/component/response/WindHttpResponse.php');

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
		$_GET['formName'] = 'UserForm';
		$response = WindHttpResponse::getInstance();
		$response->setDispatcher(WindDispatcher::getInstance());
		$this->obj->doBeforeProcess(WindHttpRequest::getInstance(), $response);
		$userForm = L::getInstance('UserForm');
		$this->assertEquals('phpwind', $userForm->name);
	}
	
	/**
	 * sendError留待WindErrorMessage测试
	 */
	public function testSetFormWithValidation() {
		$_GET['name'] = 'wind';
		$_GET['formName'] = 'UserForm';
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

