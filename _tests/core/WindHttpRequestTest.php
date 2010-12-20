<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.WindHttpRequest');
L::import('WIND:core.WindHttpResponse');
L::import('WIND:core.exception.WindException');

class WindHttpRequestTest extends BaseTestCase {
	private $httpRequest;
	private $get = array('name' => 'xxx', 'sex' => '1', 'file' => array('filename1', 'filename2'));
	private $post = array('address' => 'zhejiangU', 'step' => '5');
	private $cookie = array('logIP' => '10.1.123.99', 'loginTime' => '2010-12-17');
	public function setUp() {
		parent::setUp();
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
	private function slashesArray($param) {
		static $result = array();
		foreach ((array)$param as $key => $value) {
			if (is_array($value)) $this->slashesArray($value);
			$result[$key] = stripslashes($value);
		}
		return $result;
	}
	/**
	 */
	public function teststripSlashes() {
		foreach(self::providerSlashes() as $value) {
			$params = $value[0];
			if (is_array($params)) {
				$param = $this->slashesArray($params);
				$this->checkArray($this->httpRequest->stripSlashes($params), count($param), $param, true);
			} else {
				$this->assertEquals(stripslashes($params), $this->httpRequest->stripSlashes($params));
			}
		}
	}
	
	/**
	 */
	public function testGetAttributeFromGet() {
		foreach(self::providerGet() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getAttribute($value[0]));
	}

	/**
	 */
	public function testGetAttributeFromPost() {
		foreach(self::providerPost() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getAttribute($value[0]));
	}

	/**
	 */
	public function testGetAttributeFromCookie() {
		foreach(self::providerCookie() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getAttribute($value[0]));
	}
	
	/**
	 */
	public function testGetAttributeWithDefaultValue() {
		foreach(self::providerDefault() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getAttribute($value[0], $value[1]));
	}
	
	/**
	 */
	public function testGetQuery() {
		foreach(self::providerGet() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getQuery($value[0]));
	}
	/**
	 */
	public function testGetQueryWithDefault() {
		foreach(self::providerDefault() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getQuery($value[0], $value[1]));
	}
	
	public function testGetQuestWithNull() {
		$param = $this->httpRequest->getQuery();
		$this->checkArray($param, 3, array('name', 'sex', 'file'));
		$this->checkArray($param['file'], 2, array('filename1', 'filename2'), true);
	}

	/**
	 */
	public function testGetPost() {
		foreach(self::providerPost() as $value) 
			$this->assertEquals($value[1], $this->httpRequest->getPost($value[0]));
	}
	/**
	 */
	public function testGetPostWithDefault() {
		foreach(self::providerDefault() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getPost($value[0], $value[1]));
	}
	
	public function testGetPostWithNull() {
		$param = $this->httpRequest->getPost();
		$this->checkArray($param, 2, array('address' => 'zhejiangU', 'step' => '5'), true);
	}
	
	/**
	 */
	public function testGet() {
		foreach(self::providerGet() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getGet($value[0]));
	}
	/**
	 */
	public function testGetWithDefault() {
		foreach(self::providerDefault() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getGet($value[0], $value[1]));
	}
	
	public function testGetWithNull() {
		$param = $this->httpRequest->getGet();
		$this->checkArray($param, 3, array('name', 'sex', 'file'));
		$this->checkArray($param['file'], 2, array('filename1', 'filename2'), true);
	}
	
	/**
	 */
	public function testGetCookie() {
		foreach(self::providerCookie() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getCookie($value[0]));
	}
	/**
	 */
	public function testGetCookieWithDefault() {
		foreach(self::providerDefault() as $value)
			$this->assertEquals($value[1], $this->httpRequest->getCookie($value[0], $value[1]));
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
	
	/**
	 * WindHttpRequest会在第一次运行后保持值·· 
	 * 测试其他逻辑需要先注释已经有值的判断(//if (!$this->_clientIp)),同时去除注释
	 */
	public function testGetClientIp() {
		$this->assertTrue('0.0.0.0' == $this->httpRequest->getClientIp());
		$_SERVER['HTTP_CLIENT_IP'] = '192.168.0.200';
		/*$this->assertTrue('192.168.0.200' == $this->httpRequest->getClientIp());
		
		$_SERVER['HTTP_PROXY_USER'] = '10.1.200.23';
		$_SERVER['HTTP_CLIENT_IP'] = '';
		$this->assertTrue('10.1.200.23' == $this->httpRequest->getClientIp());
		
		$_SERVER['HTTP_PROXY_USER'] = '';
		$_SERVER['HTTP_CLIENT_IP'] = '';
		$_SERVER['REMOTE_ADDR'] = '172.36.5.2';
		$this->assertTrue('172.36.5.2' == $this->httpRequest->getClientIp());*/
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
	
	public function testGetRequestUriWithException() {
		try{
	    	$uri = $this->httpRequest->getRequestUri();
	    } catch (WindException $exception) {
	    	return;
	    }
	    $this->fail('Exception');
	}
	/**
	 * WindHttpRequest会在第一次运行后保持值·· 
	 * 测试其他逻辑需要先注释已经有值的判断(//if (!$this->_requestUri))
	 */
	public function testGetRequestUri() {
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
	
	public function testGetScriptUrlWithException() {
		try{
		    $url = $this->httpRequest->getScriptUrl();
	    } catch (WindException $exception) {
	    	return;
	    }
	    $this->fail('an exception is catched');
	}
	
	/**
	 * WindHttpRequest会在第一次运行后保持值·· 
	 * 测试其他逻辑需要先注释已经有值的判断(//if (!$this->_scriptUrl))
	 */
	public function testGetScriptUrl() {
	    $_SERVER['SCRIPT_FILENAME'] = "/usr/ppt/demos/example/index.php";
	    
		$_SERVER['SCRIPT_NAME'] = '/usr/ppt/demos/example/index.php';
		$this->assertEquals('/usr/ppt/demos/example/index.php', $this->httpRequest->getScriptUrl());
		
		$_SERVER['SCRIPT_NAME'] = '';
		$_SERVER['PHP_SELF'] = '/usr/ppt/demos/example/index.php';
		$this->assertEquals('/usr/ppt/demos/example/index.php', $this->httpRequest->getScriptUrl());
		
		$_SERVER['PHP_SELF'] = '';
		$_SERVER['ORIG_SCRIPT_NAME'] = '/usr/ppt/demos/example/index.php';
		$this->assertEquals('/usr/ppt/demos/example/index.php', $this->httpRequest->getScriptUrl());
		
		$_SERVER['ORIG_SCRIPT_NAME'] = '';
		$_SERVER['DOCUMENT_ROOT'] = 'D:/php';
		$_SERVER['SCRIPT_FILENAME'] = "D:/php/usr/ppt/demos/example/index.php";
		$this->assertEquals('/usr/ppt/demos/example/index.php', $this->httpRequest->getScriptUrl());
	
	}
	
	public function testGetScript() {
		$_SERVER['SCRIPT_FILENAME'] = "/usr/ppt/demos/example/index.php";
		$_SERVER['SCRIPT_NAME'] = '/usr/ppt/demos/example/index.php';
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
	public function testGetHostInfoWithException() {
		try{
			$this->httpRequest->getHostInfo();
		} catch(WindException $e) {
			return;
		}
		$this->fail('an exception is catched');
	}
	public function testGetHostInfo() {
		$_SERVER['HTTP_HOST'] = 'localhost:80';
		$this->assertEquals('http://localhost:80', $this->httpRequest->getHostInfo());
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['SERVER_NAME'] = 'localhost';
		$this->assertEquals('http://localhost:80', $this->httpRequest->getHostInfo());
	}
	public function testGetBaseUrl() {
		$_SERVER['SCRIPT_FILENAME'] = "/usr/ppt/demos/example/index.php";
		$_SERVER['SCRIPT_NAME'] = '/usr/ppt/demos/example/index.php';
		$_SERVER['HTTP_HOST'] = 'localhost:80';
		$this->assertEquals('http://localhost:80/usr/ppt/demos/example', $this->httpRequest->getBaseUrl(true));
		$this->assertEquals('/usr/ppt/demos/example', $this->httpRequest->getBaseUrl(false));
	}
	public function testGetPathInfoWithException() {
		try{
			$this->assertEquals('', $this->httpRequest->getPathInfo());
		}catch(WindException $e) {
			return ;
		}
		$this->fail('Exception error!');
	}
	/**
	 * WindHttpRequest会在第一次运行后保持值·· 
	 * 测试其他逻辑需要先注释已经有值的判断(//if (!$this->_pathInfo) )
	 */
	public function testGetPathInfo() {
		$_SERVER['HTTP_X_REWRITE_URL'] = '/example/index.php?a=test';
		$_SERVER['PHP_SELF'] = '/usr/ppt/demos/example/index.php?a=test';
	    $_SERVER['SCRIPT_FILENAME'] = "/usr/ppt/demos/example/index.php";
		$_SERVER['SCRIPT_NAME'] = '/usr/ppt/demos/example/index.php';
		$this->assertTrue('' == $this->httpRequest->getPathInfo());
	}
	
	public function testGetServerName() {
		$serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
		$this->assertEquals($serverName, $this->httpRequest->getServerName());
	}
    
	public function testSetServerPort() {
		$this->httpRequest->setServerPort(89);
		$this->assertEquals(89, $this->httpRequest->getServerPort());
	}
	
	public function testGetRemoteHost() {
		$value = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
		$this->assertEquals($value, $this->httpRequest->getRemoteHost());
	}
	
	public function testGetUrlReferer() {
		$value = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		$this->assertEquals($value, $this->httpRequest->getUrlReferer());
	}
	public function testGetRemotePort() {
		$value = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : null;
		$this->assertEquals($value, $this->httpRequest->getRemotePort());
	}
	public function testGetUserAgent() {
		$value = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->assertEquals($value, $this->httpRequest->getUserAgent());
	}
	public function testGetAcceptTypes() {
		$value = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
		$this->assertEquals($value, $this->httpRequest->getAcceptTypes());
	}

	public function testGetAcceptCharset() {
		$value = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
		$this->assertEquals($value, $this->httpRequest->getAcceptCharset());
	}
	
	public function testGetAcceptLanguage() {
		$value = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		$language = explode(',', $value);
	    $value = $language[0] ? $language[0] : 'zh-cn';
	    $this->assertEquals($value, $this->httpRequest->getAcceptLanguage());
	}
	public function testGetResponse() {
		$this->assertTrue($this->httpRequest->getResponse() instanceof WindHttpResponse);
	}
}