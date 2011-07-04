<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:core.request.IWindRequest');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindHttpRequest implements IWindRequest {
	/**
	 * 访问的端口号
	 * @var int
	 */
	private $_port = null;
	/**
	 * 客户端IP
	 * @var string
	 */
	private $_clientIp = null;
	/**
	 * 语言信息
	 * @var string
	 */
	private $_language = null;
	/**
	 * 路径信息
	 * @var string
	 */
	private $_pathInfo = null;
	/**
	 * @var string
	 */
	private $_scriptUrl = null;
	/**
	 * @var string
	 */
	private $_requestUri = null;
	/**
	 * 基础路径信息
	 * @var string
	 */
	private $_baseUrl = null;
	private $_hostInfo = null;
	/**
	 * 请求参数信息
	 * @var array
	 */
	private $_attribute = array();
	/**
	 * @var WindHttpResponse
	 */
	private $_response = null;

	public function __construct() {
		$this->normalizeRequest();
	}

	protected function normalizeRequest() {
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			if (isset($_GET)) $_GET = $this->stripSlashes($_GET);
			if (isset($_POST)) $_POST = $this->stripSlashes($_POST);
			if (isset($_REQUEST)) $_REQUEST = $this->stripSlashes($_REQUEST);
			if (isset($_COOKIE)) $_COOKIE = $this->stripSlashes($_COOKIE);
		}
	}

	public function stripSlashes(&$data) {
		return is_array($data) ? array_map(array($this, 'stripSlashes'), $data) : stripslashes($data);
	}

	public function setAttribute($name, $value) {
		$this->_attribute[$name] = $value;
	}

	/**
	 * 根据名称获得服务器和执行环境信息
	 * @param string|null $name
	 */
	public function getAttribute($name, $value = '') {
		if (isset($this->_attribute[$name]))
			return $this->_attribute[$name];
		else if (isset($_GET[$name]))
			return $_GET[$name];
		else if (isset($_POST[$name]))
			return $_POST[$name];
		else if (isset($_COOKIE[$name]))
			return $_COOKIE[$name];
		else if (isset($_REQUEST[$name]))
			return $_REQUEST[$name];
		else if (isset($_ENV[$name]))
			return $_ENV[$name];
		else if (isset($_SERVER[$name]))
			return $_SERVER[$name];
		else
			return $value;
	}

	/**
	 * 返回$_GET,$_POST的值，未设置则返回default
	 * @param string $name | attribute name 
	 */
	public function getRequest($name = '', $defaultValue = null) {
		if (!$name) return array_merge($_POST, $_GET);
		if (isset($_GET[$name])) return $_GET[$name];
		if (isset($_POST[$name])) return $_POST[$name];
		return $defaultValue;
	}

	/**
	 * 从query中取值
	 * 
	 * @param string $name
	 * @param string $default
	 * @return string|null
	 */
	public function getQuery($name = null, $defaultValue = null) {
		return $this->getGet($name, $defaultValue);
	}

	/**
	 * 获得post值
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string|null
	 */
	public function getPost($name = null, $defaultValue = null) {
		if ($name == null) return $_POST;
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}

	/**
	 * 获得get值
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string|null
	 */
	public function getGet($name = '', $defaultValue = null) {
		if ($name == null) return $_GET;
		return (isset($_GET[$name])) ? $_GET[$name] : $defaultValue;
	}

	/**
	 * 返回cookie的值，如果$name=null则返回所有Cookie值
	 * 
	 * @param string $key
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getCookie($name = null, $defaultValue = null) {
		if ($name == null) return $_COOKIE;
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $defaultValue;
	}

	/**
	 * 返回session的值，如果$name=null则返回所有Cookie值
	 * 
	 * @param string $key
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getSession($name = null, $defaultValue = null) {
		if ($name == null) return $_SESSION;
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : $defaultValue;
	}

	/**
	 * 返回Server的值，如果$name为空则返回所有Server的值
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getServer($name = null, $defaultValue = null) {
		if ($name == null) return $_SERVER;
		return (isset($_SERVER[$name])) ? $_SERVER[$name] : $defaultValue;
	}

	/**
	 * 返回env中的值，如果$name为null则返回所有env的值
	 * 
	 * @param string|null $name
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getEnv($name = null, $defaultValue = null) {
		if ($name == null) return $_ENV;
		return (isset($_ENV[$name])) ? $_ENV[$name] : $defaultValue;
	}

	/**
	 * 获取协议名称
	 * 
	 * @return string
	 */
	public function getScheme() {
		return ($this->getServer('HTTPS') == 'on') ? 'https' : 'http';
	}

	/**
	 * 返回请求页面时通信协议的名称和版本
	 * @return string
	 */
	public function getProtocol() {
		return $this->getServer('SERVER_PROTOCOL', 'HTTP/1.0');
	}

	/**
	 * 返回访问IP
	 * 
	 * @return string|0.0.0.0
	 */
	public function getClientIp() {
		if (!$this->_clientIp) $this->_getClientIp();
		return $this->_clientIp;
	}

	/**
	 * 获得请求的方法
	 */
	public function getRequestMethod() {
		return strtoupper($this->getServer('REQUEST_METHOD'));
	}

	/**
	 * 获得请求类型
	 * 
	 * @return string
	 */
	public function getRequestType() {
		return IWindRequest::REQUEST_TYPE_WEB;
	}

	/**
	 * 返回该请求是否为ajax请求
	 * @return Boolean
	 */
	public function getIsAjaxRequest() {
		return !strcasecmp($this->getServer('HTTP_X_REQUESTED_WITH'), 'XMLHttpRequest');
	}

	/**
	 * Returns a boolean indicating whether this request was made using a
	 * secure channel, such as HTTPS.
	 * @return Boolean
	 */
	public function isSecure() {
		return !strcasecmp($this->getServer('HTTPS'), 'on');
	}

	/**
	 * 返回请求是否为GET请求类型
	 * @return boolean 
	 */
	public function isGet() {
		return !strcasecmp($this->getRequestMethod(), 'GET');
	}

	/**
	 * 返回请求是否为POST请求类型
	 * @return boolean
	 */
	public function isPost() {
		return !strcasecmp($this->getRequestMethod(), 'POST');
	}

	/**
	 * 返回请求是否为PUT请求类型
	 * @return boolean
	 */
	public function isPut() {
		return !strcasecmp($this->getRequestMethod(), 'PUT');
	}

	/**
	 * 返回请求是否为DELETE请求类型
	 * @return boolean
	 */
	public function isDelete() {
		return !strcasecmp($this->getRequestMethod(), 'Delete');
	}

	/**
	 * 初始化请求的资源标识符
	 * 这里的uri是去除协议名、主机名的
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_requestUri = /example/index.php?a=test
	 * 
	 * @return string
	 */
	public function getRequestUri() {
		if (!$this->_requestUri) $this->initRequestUri();
		return $this->_requestUri;
	}

	/**
	 * 返回当前执行脚本的绝对路径
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_scriptUrl = /example/index.php
	 * 
	 * @throws WindException
	 * @return string
	 */
	public function getScriptUrl() {
		if (!$this->_scriptUrl) $this->_initScriptUrl();
		return $this->_scriptUrl;
	}

	/**
	 * 返回执行脚本
	 */
	public function getScript() {
		if (($pos = strrpos($this->getScriptUrl(), '/')) === false) $pos = -1;
		return substr($this->getScriptUrl(), $pos + 1);
	}

	/**
	 * 获取Http头信息
	 * @param string $header 头部名称
	 * @return string|null
	 */
	public function getHeader($header, $default = null) {
		$temp = strtoupper(str_replace('-', '_', $header));
		if (substr($temp, 0, 5) != 'HTTP_') $temp = 'HTTP_' . $temp;
		if (($header = $this->getServer($temp)) != null) return $header;
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if ($headers[$header]) return $headers[$header];
		}
		return $default;
	}

	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 * 
	 * @throws WindException
	 * @return string
	 */
	public function getPathInfo() {
		if (!$this->_pathInfo) $this->_initPathInfo();
		return $this->_pathInfo;
	}

	/**
	 * 获取基础URL,这里是去除了脚本文件以及访问参数信息的URL地址信息
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_baseUrl = example
	 * return absolute url address when absolute is true 
	 * 'example' will be return when absolute is false
	 * 'http://www.phpwind.net/example' will be return when absolute is true
	 * 'http://www.phpwind.net:80/example' will be return when absolute is true
	 * 'http://www.phpwind.net:443/example' will be return when absolute is true
	 * 
	 * @param boolean $absolute
	 * @return string
	 */
	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === null) $this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/.');
		return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
	}

	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 * 
	 * @return string
	 */
	public function getHostInfo() {
		if ($this->_hostInfo === null) $this->_initHostInfo();
		return $this->_hostInfo;
	}

	/**
	 * 返回当前运行脚本所在的服务器的主机名。
	 * 如果脚本运行于虚拟主机中
	 * 该名称是由那个虚拟主机所设置的值决定
	 * 
	 * @return string|''
	 */
	public function getServerName() {
		return $this->getServer('SERVER_NAME', '');
	}

	/**
	 * 返回服务端口号
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 * 
	 * @return int
	 */
	public function getServerPort() {
		if (!$this->_port) {
			$_default = $this->isSecure() ? 443 : 80;
			$this->setServerPort($this->getServer('SERVER_PORT', $_default));
		}
		return $this->_port;
	}

	/**
	 * 设置服务端口号
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 * 
	 * @param int $port
	 */
	public function setServerPort($port) {
		$this->_port = (int) $port;
	}

	/**
	 * 返回浏览当前页面的用户的主机名
	 * DNS 反向解析不依赖于用户的 REMOTE_ADDR
	 * 
	 * @return string|null
	 */
	public function getRemoteHost() {
		return $this->getServer('REMOTE_HOST');
	}

	/**
	 * 返回浏览器发送Referer请求头，可以让服务器了解和追踪发出本次请求的起源URL地址
	 * 
	 * @return string|null 
	 */
	public function getUrlReferer() {
		return $this->getServer('HTTP_REFERER');
	}

	/**
	 * 获得用户机器上连接到 Web 服务器所使用的端口号
	 * 
	 * @return number|null
	 */
	public function getRemotePort() {
		return $this->getServer('REMOTE_PORT');
	}

	/**
	 * 返回User-Agent头字段用于指定浏览器或者其他客户端程序的类型和名字
	 * 如果客户机是一种无线手持终端，就返回一个WML文件；如果发现客户端是一种普通浏览器，
	 * 则返回通常的HTML文件
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return $this->getServer('HTTP_USER_AGENT', '');
	}

	/**
	 * 返回当前请求头中 Accept: 项的内容，
	 * Accept头字段用于指出客户端程序能够处理的MIME类型，例如 text/html,image/*
	 * 
	 * @return string|''
	 */
	public function getAcceptTypes() {
		return $this->getServer('HTTP_ACCEPT', '');
	}

	/**
	 * 返回客户端程序可以能够进行解码的数据编码方式，这里的编码方式通常指某种压缩方式
	 * 
	 * @return string|''
	 */
	public function getAcceptCharset() {
		return $this->getServer('HTTP_ACCEPT_ENCODING', '');
	}

	/**
	 * 返回客户端程序期望服务器返回哪个国家的语言文档 
	 * Accept-Language: en-us,zh-cn
	 * 
	 * @return string
	 */
	public function getAcceptLanguage() {
		if (!$this->_language) {
			$_language = explode(',', $this->getServer('HTTP_ACCEPT_LANGUAGE', ''));
			$this->_language = $_language[0] ? $_language[0] : 'zh-cn';
		}
		return $this->_language;
	}

	/**
	 * 获得返回信息
	 * @return WindHttpResponse
	 */
	public function getResponse() {
		if ($this->_response === null) {
			Wind::import('WIND:core.response.WindHttpResponse');
			$this->_response = new WindHttpResponse();
			if ($this->getIsAjaxRequest()) {
				$this->_response->addHeader('Content-type', 'text/xml;charset=utf-8');
				$this->_response->setIsAjax(true);
			} else
				$this->_response->addHeader('Content-type', 'text/html;charset=utf-8');
		}
		return $this->_response;
	}

	/**
	 * 返回访问的IP地址
	 * 
	 * Example:
	 * $this->_clientIp = 127.0.0.1
	 * 
	 * @return string 
	 */
	private function _getClientIp() {
		if (($ip = $this->getServer('HTTP_CLIENT_IP')) != null) {
			$this->_clientIp = $ip;
		} elseif (($_ip = $this->getServer('HTTP_X_FORWARDED_FOR')) != null) {
			$ip = strtok($_ip, ',');
			do {
				$ip = ip2long($ip);
				if (!(($ip == 0) || ($ip == 0xFFFFFFFF) || ($ip == 0x7F000001) || (($ip >= 0x0A000000) && ($ip <= 0x0AFFFFFF)) || (($ip >= 0xC0A8FFFF) && ($ip <= 0xC0A80000)) || (($ip >= 0xAC1FFFFF) && ($ip <= 0xAC100000)))) {
					$this->_clientIp = long2ip($ip);
					return;
				}
			} while (($ip = strtok(',')));
		} elseif (($ip = $this->getServer('HTTP_PROXY_USER')) != null) {
			$this->_clientIp = $ip;
		} elseif (($ip = $this->getServer('REMOTE_ADDR')) != null) {
			$this->_clientIp = $ip;
		} else {
			$this->_clientIp = "0.0.0.0";
		}
	}

	/**
	 * 初始化请求的资源标识符
	 * 这里的uri是去除协议名、主机名的
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_requestUri = /example/index.php?a=test
	 * 
	 * @throws WindException
	 */
	private function initRequestUri() {
		if (($requestUri = $this->getServer('HTTP_X_REWRITE_URL')) != null) {
			$this->_requestUri = $requestUri;
		} elseif (($requestUri = $this->getServer('REQUEST_URI')) != null) {
			$this->_requestUri = $requestUri;
			if (strpos($this->_requestUri, $this->getServer('HTTP_HOST')) !== false) $this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
		} elseif (($requestUri = $this->getServer('ORIG_PATH_INFO')) != null) {
			$this->_requestUri = $requestUri;
			if (($query = $this->getServer('QUERY_STRING')) != null) $this->_requestUri .= '?' . $query;
		} else
			throw new WindException(__CLASS__ . ' is unable to determine the request URI.');
	}

	/**
	 * 初始化当前执行脚本的绝对路径
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_scriptUrl = /example/index.php
	 * 
	 * @throws WindException
	 * @return
	 */
	private function _initScriptUrl() {
		if (($scriptName = $this->getServer('SCRIPT_FILENAME')) == null) throw new WindException(__CLASS__ . ' determine the entry script URL failed!!!');
		$scriptName = basename($scriptName);
		if (($_scriptName = $this->getServer('SCRIPT_NAME')) != null && basename($_scriptName) === $scriptName) {
			$this->_scriptUrl = $_scriptName;
		} elseif (($_scriptName = $this->getServer('PHP_SELF')) != null && basename($_scriptName) === $scriptName) {
			$this->_scriptUrl = $_scriptName;
		} elseif (($_scriptName = $this->getServer('ORIG_SCRIPT_NAME')) != null && basename($_scriptName) === $scriptName) {
			$this->_scriptUrl = $_scriptName;
		} elseif (($pos = strpos($this->getServer('PHP_SELF'), '/' . $scriptName)) !== false) {
			$this->_scriptUrl = substr($this->getServer('SCRIPT_NAME'), 0, $pos) . '/' . $scriptName;
		} elseif (($_documentRoot = $this->getServer('DOCUMENT_ROOT')) != null && ($_scriptName = $this->getServer('SCRIPT_FILENAME')) != null && strpos($_scriptName, $_documentRoot) === 0) {
			$this->_scriptUrl = str_replace('\\', '/', str_replace($_documentRoot, '', $_scriptName));
		} else
			throw new WindException(__CLASS__ . ' determine the entry script URL failed!!');
	}

	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_hostInfo = http://www.phpwind.net/
	 * $this->_hostInfo = http://www.phpwind.net:80/
	 * $this->_hostInfo = https://www.phpwind.net:443/
	 * 
	 * @throws WindException
	 * @return 
	 */
	private function _initHostInfo() {
		$http = $this->isSecure() ? 'https' : 'http';
		if (($httpHost = $this->getServer('HTTP_HOST')) != null)
			$this->_hostInfo = $http . '://' . $httpHost;
		elseif (($httpHost = $this->getServer('SERVER_NAME')) != null) {
			$this->_hostInfo = $http . '://' . $httpHost;
			if (($port = $this->getServerPort()) != null) $this->_hostInfo .= ':' . $port;
		} else
			throw new WindException(__CLASS__ . ' determine the entry script URL failed!!');
	}

	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 * 
	 * @throws WindException
	 * @return
	 */
	private function _initPathInfo() {
		$requestUri = urldecode($this->getRequestUri());
		$scriptUrl = $this->getScriptUrl();
		$baseUrl = $this->getBaseUrl();
		if (strpos($requestUri, $scriptUrl) === 0)
			$pathInfo = substr($requestUri, strlen($scriptUrl));
		elseif ($baseUrl === '' || strpos($requestUri, $baseUrl) === 0)
			$pathInfo = substr($requestUri, strlen($baseUrl));
		elseif (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
			$pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
		else
			throw new WindException('');
		if (($pos = strpos($pathInfo, '?')) !== false) $pathInfo = substr($pathInfo, 0, $pos);
		$this->_pathInfo = trim($pathInfo, '/');
	}
}