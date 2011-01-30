<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * WindHttpResponse单元测试
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindHttpResponseTest extends BaseTestCase {
	private $httpResponse;
	public function setUp() {
		parent::setUp();
		require_once('core/response/WindHttpResponse.php');
		require_once('core/exception/WindException.php');
		$this->httpResponse = WindHttpResponse::getInstance();
	}
	public static function providerSets() {
		return array(
			array('name', 'xxx', false),
			array('id_name', 'xjx', true),
			array('-id_2', 'phpwind', false),
			array('___2r_time', '2010', true),
			array('--Pear---_lod', 'xiaowai', false),
			array('-pear_id', '', false),
			array(' ', 'contents', false),
			array('', '', true),
			array(' ', '', true),
		);
	}
	public static function providerAdd() {
		return array(
			array('name', 'phpSchool', true),
			array('', '', false),
			array('_id_name', 'hello', false),
			array('-pear_id', 'iphone', true),
			array('--Pear----lod', 'xiaonei', false),
			array('-banan-QQ', '', false),
			array('', 'contents', false),
			array(' ', '', false),
		);
	}
	public static function providerStatus() {
		return array(
			array('200', ''),
			array(10, ''),
			array(505, 'errorMessage no is 505'),
			array(499, '499'),
			array(400, 'errorInfo no is 400'),
			array(399, 'errorInfo no is 399'),
			array(300, 'errorInfo no is 300'),
			array(array(22), ''),
		);
	}
	public static function providerBody() {
		return array(
			array('i am content', ''),
			array('i am header', 'header'),
			array('i am body', array('body')),
			array(array('i am lucy'), 'body'),
			array('', 'footer'),
			array('', ''),
		);
	}
	private function checkArray($array, $num, $member = array(), $flag = false) {
		$this->assertTrue(is_array($array));
		$this->assertEquals($num, count($array));
		if (!$member) return;
		foreach ((array)$member as $key => $value) {
			if (!$flag) {
				$this->assertTrue(isset($array[$value]));
				continue;
			}
			$this->assertTrue(isset($array[$key]) && $value == $array[$key]); 
		}
	}
	
	/**
	 * @dataProvider providerAdd
	 */
	public function testAddHeader($name, $value, $replace) {
		$this->httpResponse->addHeader($name, $value, $replace);
		$this->isTrue('OK');
	}
	
	public function testGetHeaderForAdd() {
		$header = $this->httpResponse->getHeaders();
		$this->checkArray($header, 4);
		$this->checkArray($header[0], 3, array('name' => 'Name', 'value' => 'phpSchool', 'replace' => true), true);
		$this->checkArray($header[1], 3, array('name' => '-Id-Name', 'value' => 'hello', 'replace' => false), true);
		$this->checkArray($header[2], 3, array('name' => '-Pear-Id', 'value' => 'iphone', 'replace' => true), true);
		$this->checkArray($header[3], 3, array('name' => '--Pear----Lod', 'value' => 'xiaonei', 'replace' => false), true);
	}
   
	/**
	 * @dataProvider providerSets
	 */
	public function testSetHeader($name, $value, $replace) {
		$this->httpResponse->setHeader($name, $value, $replace);
		$this->isTrue('OK');
	}
	
	public function testGetHeaderForSet() {
		$header = $this->httpResponse->getHeaders();
		$this->checkArray($header, 4);
		$this->checkArray($header[0], 3, array('name' => 'Name', 'value' => 'xxx', 'replace' => false), true);
		$this->checkArray($header[1], 3, array('name' => '-Id-Name', 'value' => 'hello', 'replace' => false), true);
		$this->checkArray($header[2], 3, array('name' => '-Pear-Id', 'value' => 'iphone', 'replace' => true), true);
		$this->checkArray($header[3], 3, array('name' => '--Pear----Lod', 'value' => 'xiaowai', 'replace' => false), true);
	}
	
	public function testClearHeaders() {
		$this->httpResponse->clearHeaders();
		$header = $this->httpResponse->getHeaders();
		$this->checkArray($header, 0);
	}
	/**
	 * @dataProvider providerStatus
	 */
	public function testSetStatus($no, $message) {
		$this->httpResponse->setStatus($no, $message);
		$this->isTrue('OK');
	}
	
	/**
	 * @dataProvider providerBody
	 * @param string $content
	 * @param string $name
	 */
	public function testSetBody($content, $name) {
		$this->httpResponse->setBody($content, $name);
		$this->isTrue('OK');
	}

	public function testGetBodyByName() {
		$this->assertEquals('i am body', $this->httpResponse->getBody('default'));
		$this->assertEquals('', $this->httpResponse->getBody('footer'));
		$this->assertEquals(null, $this->httpResponse->getBody('body'));
		$this->assertEquals(null, $this->httpResponse->getBody(array('default')));
		$this->assertEquals('i am header', $this->httpResponse->getBody('header'));
	}
	public function testGetBodyByFalse() {
		$content = array('default' => 'i am body',
					'header' => 'i am header');
		$this->assertEquals(implode('', $content), $this->httpResponse->getBody(false));
	}
	public function testGetBodyByTrue() {
		$content = array('default' => 'i am body',
					'header' => 'i am header');
		$this->checkArray($this->httpResponse->getBody(true), 2, $content, true);
	}
	
	public function testSendBody() {
		$content = array('default' => 'i am body',
					'header' => 'i am header');
		ob_start();
		$this->httpResponse->sendBody();
		$value = ob_get_clean();
		$this->assertEquals(implode('', $content), $value);
	}
	
	public function testClearBody() {
		$this->httpResponse->clearBody();
		$body = $this->httpResponse->getBody(true);
		$this->checkArray($body, 0);
	}
	
	public function testSetDispatcher() {
		$this->httpResponse->setDispatcher(array('dispatcher'));
		$this->isTrue('OK');
	}
	public function testGetDispatcher() {
		$dispatcher = $this->httpResponse->getDispatcher();
		$this->assertEquals('dispatcher', $dispatcher[0]);
	}
	
	public function testIsSendedHeader() {
		$this->assertFalse($this->httpResponse->isSendedHeader());
		/*echo 'send Header';
		$this->assertTrue($this->httpResponse->isSendedHeader());*/
		try{
			$this->httpResponse->isSendedHeader(true);
		} catch(Exception $e) {
			$this->assertTrue('WindException' == get_class($e));
			return;
		}
	}
	
	/**
	 * @dataProvider providerStatus
	 * @param unknown_type $no
	 * @param unknown_type $message
	 */
	public function testSendError($no, $message) {
		$this->httpResponse->sendError($no, $message);
		$this->isTrue('OK');
	}
	
	public function testSendErrorWithBody() {
		$body = $this->httpResponse->getBody(true);
		$this->checkArray($body, 1, array('default' => 'errorInfo no is 400'), true);
	}
	
	public function testSendRedirect() {
		throw new PHPUnit_Framework_IncompleteTestError('TODO');
	}

	public function testSendResponse() {
		throw new PHPUnit_Framework_IncompleteTestError('TODO');
	}
	public function testSendHeaders() {
		throw new PHPUnit_Framework_IncompleteTestError('TODO');
	}
}