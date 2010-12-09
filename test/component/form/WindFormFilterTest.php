<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestSetting.php');
require_once(R_P . '/component/form/WindFormFilter.php');
require_once(R_P . '/component/request/WindHttpRequest.php');
require_once(R_P . '/component/response/WindHttpResponse.php');

class WindFormFilterTest extends PHPUnit_Framework_TestCase {
	private $obj;
	private $request;
	private $response;
	public function setUp() {
		$this->obj = new WindFormFilter();
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
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
}

