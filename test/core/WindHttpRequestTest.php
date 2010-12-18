<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once (dirname(dirname(__FILE__)) . '/BaseTestCase.php');
L::import('WIND:core.WindHttpRequest');
L::import('WIND:core.exception.WindException');


class WindHttpRequestTest extends BaseTestCase {
	private $httpRequest;
	private $get = array('name' => 'xxx', 'sex' => '1', 'file' => array('filename1', 'filename2'));
	private $post = array('address' => 'zhejiangU', 'step' => '5');
	private $cookie = array('logIP' => '10.1.123.99', 'loginTime' => '2010-12-17');
	public function setUp() {
		$_GET = array();
		$_POST = array();
		$_COOKIE = array();
		$_GET['name'] = 'xxx';
		$_GET['sex'] = '1';
		$_GET['file'] = array('filename1', 'filename2');
		$_POST['address'] = 'zhejiangU';
		$_POST['step'] = '5';
		$_COOKIE['logIP'] = '10.1.123.99';
		$_COOKIE['loginTime'] = '2010-12-17';
		$this->httpRequest = WindHttpRequest::getInstance();
	}
	
	public static function providerSlashes() {
		return array(
			array('name'),
			array(array('p' => "pear'", 'a' => 'apple\'s', 'c' => '\\\\')),
			array(10),
			array($this),
			array(''),
			array(null),
		);
	}
	
	public static function providerGet() {
		return array(
		    array('name', 'xxx'),
		    array('sex', '1'),
		    array('school', ''),
		);
	}
	public static function providerPost() {
		return array(
		    array('address', 'zhejiangU'),
		    array('step', '5'),
		    array('town', ''),
		);
	}
	public static function providerCookie() {
		return array(
		    array('logIP', '10.1.123.99'),
		    array('loginTime', '2010-12-17'),
		    array('cookieName', ''),
		);
	}
	
	public static function providerDefault() {
		return array(
		    array('defaultName', 'thankYou'),
		    array('defaultValue', 'haha'),
		    array('helloWorld', 'me too'),
		    array('empty', ''),
		);
	}
	
	private function checkArray($array, $num, $member = array(), $flag = false) {
		$this->assertTrue(is_array($array));
		$this->assertTrue(count($array) == $num);
		if (!$member) return;
		foreach ((array)$member as $key => $value) {
			($flag) ? $this->assertEquals($value, $array[$key]) :
					$this->assertTrue(isset($array[$value]));
		}
	}
	private function searchArray($param) {
		static $result = array();
		foreach ($param as $key => $value) {
			if (is_array($value)) $this->searchArray($value);
			$result[$key] = stripslashes($value);
		}
		return $result;
	}
	/**
	 * @dataProvider providerSlashes
	 * @param unknown_type $param
	 */
	public function teststripSlashes($params) {
		if (is_array($params)) {
			$param = $this->searchArray($params);
		} else {
			$param = stripslashes($params);
		}
		$this->assertEquals($param, $this->httpRequest->stripSlashes($params));
	}
	
	/**
	 * @dataProvider providerGet
	 */
	public function testGetAttributeFromGet($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getAttribute($name));
	}

	/**
	 * @dataProvider providerPost
	 */
	public function testGetAttributeFromPost($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getAttribute($name));
	}

	/**
	 * @dataProvider providerCookie
	 */
	public function testGetAttributeFromCookie($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getAttribute($name));
	}
	
	/**
	 * @dataProvider providerDefault
	 */
	public function testGetAttributeWithDefaultValue($name, $defaultValue) {
		$this->assertEquals($defaultValue, $this->httpRequest->getAttribute($name, $defaultValue));
	}
	
	/**
	 * @dataProvider providerGet
	 */
	public function testGetQuery($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getQuery($name));
	}
	/**
	 * @dataProvider providerDefault
	 */
	public function testGetQueryWithDefault($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getQuery($name, $value));
	}
	
	public function testGetQuestWithNull() {
		$param = $this->httpRequest->getQuery();
		$this->checkArray($param, 3, array('name', 'sex', 'file'));
		$this->checkArray($param['file'], 2, array('filename1', 'filename2'), true);
	}

	/**
	 * @dataProvider providerPost
	 */
	public function testGetPost($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getPost($name));
	}
	/**
	 * @dataProvider providerDefault
	 */
	public function testGetPostWithDefault($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getPost($name, $value));
	}
	public function testGetPostWithNull() {
		$param = $this->httpRequest->getPost();
		$this->checkArray($param, 2, array('address' => 'zhejiangU', 'step' => '5'), true);
	}
	
	/**
	 * @dataProvider providerGet
	 */
	public function testGet($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getGet($name));
	}
	/**
	 * @dataProvider providerDefault
	 */
	public function testGetWithDefault($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getGet($name, $value));
	}
	
	public function testGetWithNull() {
		$param = $this->httpRequest->getGet();
		$this->checkArray($param, 3, array('name', 'sex', 'file'));
		$this->checkArray($param['file'], 2, array('filename1', 'filename2'), true);
	}
	
	/**
	 * @dataProvider providerCookie
	 */
	public function testGetCookie($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getCookie($name));
	}
	/**
	 * @dataProvider providerDefault
	 */
	public function testGetCookieWithDefault($name, $value) {
		$this->assertEquals($value, $this->httpRequest->getCookie($name, $value));
	}
	
	public function testGetCookieWithNull() {
		$param = $this->httpRequest->getCookie();
		$this->checkArray($param, 2, array('logIP' => '10.1.123.99', 'loginTime' => '2010-12-17'), true);
	}
	
	public function testGetSession() {
		$_SESSION = array();
		$_SESSION['isAdmin'] = 'true';
		$_SESSION['goods'] = '1213';
		$this->assertEquals('true', $this->httpRequest->getSession('isAdmin'));
		$this->assertEquals('1213', $this->httpRequest->getSession('goods'));
		$this->assertEquals('', $this->httpRequest->getSession('name', ''));
		$this->assertEquals('xxx', $this->httpRequest->getSession('name', 'xxx'));
		$param = $this->httpRequest->getSession();
		$this->checkArray($param, 2, array('isAdmin' => 'true', 'goods' => '1213'), true);
	}
	
	public function testGetServer() {
		$_SERVER = array();
		$_SERVER['GLOBAL'] = 'D;//yes';
		$_SERVER['hahah'] = 'werwe';
		$this->assertEquals('D;//yes', $this->httpRequest->getServer('GLOBAL'));
		$this->assertEquals('werwe', $this->httpRequest->getServer('hahah'));
		$this->assertEquals('', $this->httpRequest->getServer('name', ''));
		$this->assertEquals('xxx', $this->httpRequest->getServer('name', 'xxx'));
		$param = $this->httpRequest->getServer();
		$this->checkArray($param, 2, array('GLOBAL' => 'D;//yes', 'hahah' => 'werwe'), true);
	}
	
	public function testGetEnv() {
		$_ENV = array();
		$_ENV['enviroment'] = 'apache2';
		$_ENV['version'] = '2.2';
		$this->assertEquals('apache2', $this->httpRequest->getEnv('enviroment'));
		$this->assertEquals('2.2', $this->httpRequest->getEnv('version'));
		$this->assertEquals('', $this->httpRequest->getEnv('name', ''));
		$this->assertEquals('xxx', $this->httpRequest->getEnv('name', 'xxx'));
		$param = $this->httpRequest->getEnv();
		$this->checkArray($param, 2, array('enviroment' => 'apache2', 'version' => '2.2'), true);
	}
	
	public function testGetScheme() {
		$scheme = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$this->assertTrue($scheme == $this->httpRequest->getScheme());
	}

	public function testGetProtocol() {
		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
		$this->assertTrue($protocol == $this->httpRequest->getProtocol());
	}
	
	public function testGetClientIp() {
		$this->assertTrue('0.0.0.0' == $this->httpRequest->getClientIp());
	}
	
	public function testGetRequestMethod() {
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
		$this->assertEquals($method, $this->httpRequest->getRequestMethod());
	}
	
	public function testGetRequestType() {
		$this->assertEquals(IWindRequest::REQUEST_TYPE_WEB, $this->httpRequest->getRequestType());
	}
	
	public function testGetIsAjaxRequest() {
		$this->assertFalse($this->httpRequest->getIsAjaxRequest());
	}
	
	public function testIsSecure() {
		$this->assertFalse($this->httpRequest->isSecure());
	}
	public function testIsGet() {
		$this->assertFalse($this->httpRequest->isGet());
	}
	public function testIsPost() {
		$this->assertFalse($this->httpRequest->isPost());
	}
	public function testIsPut() {
		$this->assertFalse($this->httpRequest->isPut());
	}
	public function testIsDelete() {
		$this->assertFalse($this->httpRequest->isDelete());
	}
	
	/**
	 * WindHttpRequest会在第一次运行后保持值·· 
	 * 测试其他逻辑需要先注释已经有值的判断(//if (!$this->_requestUri))
	 */
	public function testGetRequestUri() {
		try{
	    	$uri = $this->httpRequest->getRequestUri();
	    } catch (Exception $exception) {
	    	$this->isFalse('yes');
	    }
		$_SERVER['HTTP_X_REWRITE_URL'] = '/example/index.php?a=test';
		$this->assertEquals('/example/index.php?a=test', $this->httpRequest->getRequestUri());
		
		$_SERVER['HTTP_X_REWRITE_URL'] = '';
		$_SERVER['HTTP_HOST'] = 'http://www.phpwind.net';
		$_SERVER['REQUEST_URI'] = 'http://www.phpwind.net/example/index.php?a=test';
		$this->assertEquals('/example/index.php?a=test', $this->httpRequest->getRequestUri());
		
		$_SERVER['REQUEST_URI'] = '';
		$_SERVER['ORIG_PATH_INFO'] = '/example/index.php';
		$_SERVER['QUERY_STRING'] = 'a=test';
		$this->assertEquals('/example/index.php?a=test', $this->httpRequest->getRequestUri());
	}
	public function testGetScriptUrl() {
		try{
		    $url = $this->httpRequest->getScriptUrl();
	    } catch (Exception $exception) {
	    	$this->isFalse('yes');
	    }
	    $_SERVER['SCRIPT_FILENAME'] = "/usr/ppt/demos/index.php";
	    
		$_SERVER['SCRIPT_NAME'] = '/usr/ppt/demos/index.php';
		$this->assertEquals('/usr/ppt/demos/index.php', $this->httpRequest->getScriptUrl());
		
		$_SERVER['SCRIPT_NAME'] = '';
		$_SERVER['PHP_SELF'] = '/usr/ppt/demos/index.php';
		$this->assertEquals('/usr/ppt/demos/index.php', $this->httpRequest->getScriptUrl());
		
		$_SERVER['PHP_SELF'] = '';
		$_SERVER['ORIG_SCRIPT_NAME'] = '/usr/ppt/demos/index.php';
		$this->assertEquals('/usr/ppt/demos/index.php', $this->httpRequest->getScriptUrl());
		
		$_SERVER['ORIG_SCRIPT_NAME'] = '';
		$_SERVER['DOCUMENT_ROOT'] = 'D:/php';
		$_SERVER['SCRIPT_FILENAME'] = "D:/php/usr/ppt/demos/index.php";
		$this->assertEquals('/usr/ppt/demos/index.php', $this->httpRequest->getScriptUrl());
	}
	
	public function testGetScript() {
		$this->assertEquals('index.php', $this->httpRequest->getScript());
	}
	
	public function testGetHeader() {
		$this->assertFalse($this->httpRequest->getHeader('accept'));
		$_SERVER['HTTP_ACCEPT'] = 'HTTP1.0';
		$this->assertEquals('HTTP1.0', $this->httpRequest->getHeader('accept'));
		$this->assertEquals('HTTP1.0', $this->httpRequest->getHeader('http-accept'));
		$this->assertEquals('HTTP1.0', $this->httpRequest->getHeader('ACCEPT'));
	}
	public function testGetServerPort() {
		$default = $this->httpRequest->isSecure() ? 443 : 80;
		$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : $default;
		$this->assertEquals($port, $this->httpRequest->getServerPort());
	}
	
	public function testGetHostInfo() {
		try{
			$this->httpRequest->getHostInfo();
		} catch(Exception $e) {
			$this->isFalse('false');
		}
		$_SERVER['HTTP_HOST'] = 'localhost:80';
		$this->assertEquals('http://localhost:80', $this->httpRequest->getHostInfo());
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['SERVER_NAME'] = 'localhost';
		$this->assertEquals('http://localhost:80', $this->httpRequest->getHostInfo());
	}
	public function testGetBaseUrl() {
		$url = '/usr/ppt/demos';
		$this->assertEquals('http://localhost:80/usr/ppt/demos', $this->httpRequest->getBaseUrl(true));
		$this->assertEquals('/usr/ppt/demos', $this->httpRequest->getBaseUrl(false));
	}
	public function testGetPathInfo() {
		
	}
}

