<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * WindFormFilter单元测试
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */

require_once('core/base/WindModule.php');
require_once('component/form/WindActionForm.php');
class UserFormTest extends WindActionForm {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;
	public function __construct() {
		parent::__construct();
		$this->setErrorAction('error');
		$this->setIsValidation(true);
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
		parent::setUp();
		require_once('component/form/WindFormFilter.php');
		require_once('core/WindHttpRequest.php');
		require_once('core/WindHttpResponse.php');
		require_once('core/WindSystemConfig.php');
		$array = array('extensionConfig' => array('formConfig' => 'component.form.WindFormFilterTest.php'));
		$systemConfig = new WindSystemConfig($array);
		$_GET['name'] = 'phpwind';
		$_GET['address'] = 'hz';
		$_GET['nick'] = 'xiaxia';
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
		$this->response->setData($systemConfig, 'WindSystemConfig');
		$this->obj = new WindFormFilter();
	}
	public function tearDown() {
		$this->obj = null;
		$this->request = null;
		$this->response = null;
	}
	public function testNullForm() {
		$p = $this->obj->doBeforeProcess($this->request, $this->response);
		$this->assertEquals(null, $p);
	}
	
	public function testSetFormNoValidation() {
		$_GET['formName'] = 'UserFormTest';
		throw new PHPUnit_Framework_IncompleteTestError('No complete');
		$this->obj->doBeforeProcess($this->request, $this->response);
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
