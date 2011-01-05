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
		$array = array('extensionConfig' => array('formConfig' => 'TEST:data.formConfig'));
		$systemConfig = new WindSystemConfig($array);
		$_GET['name'] = 'phpwind';
		$_GET['address'] = 'hz';
		$_GET['nick'] = 'xiaxia';
		$_GET['formName'] = 'TestForm';
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
		$this->response->setData($systemConfig, 'WindSystemConfig');
		$dispatcher = new WindWebDispatcher($this->request, $this->response);
		$dispatcher->module = 'default';
		$this->response->setDispatcher($dispatcher);
		L::register(dirname(dirname(dirname(__FILE__))) . D_S, 'TEST');
		$this->obj = new WindFormFilter();
	}
	public function tearDown() {
		parent::setUp();
		$this->obj = null;
		$this->request = null;
		$this->response = null;
	}
	public function testDoBeforeProcess() {
		$this->obj->doBeforeProcess($this->request, $this->response);
		$formObj = $this->response->getData('TestForm');
		$this->assertTrue($formObj instanceof WindActionForm);
	}
}
