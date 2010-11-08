<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WHttpRequest extends WModule implements WRequest {
	
	private $_port = null;
	
	private $_language = null;
	private $_pathInfo = null;
	private $_scriptUrl = null;
	private $_requestUri = null;
	private $_baseUrl = null;
	
	/**
	 * 根据名称获得服务器和执行环境信息,如果名称不存在则返回NULL
	 * 
	 * @param string $name
	 */
	function getAttribute($name) {
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}
	
	/**
	 * 获得post值
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string
	 */
	public function getPost($name = '', $defaultValue = null) {
		return !$name ? $defaultValue : isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}
	
	/**
	 * 获得get值
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string
	 */
	public function getGet($name = '', $defaultValue = null) {
		return !$name ? $defaultValue : isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}
	
	/**
	 * @param unknown_type $name
	 */
	public function getParameterValues($name, $defaultValue = null) {
		return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
	}
	
	/**
	 * 返回请求页面时通信协议的名称和版本
	 * @return string
	 */
	public function getProtocol() {
		return isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
	}
	
	/**
	 * 返回当前执行脚本的绝对路径
	 * @return string
	 */
	public function getScriptUrl() {
		if ($this->_scriptUrl === null) {
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if (basename($_SERVER['SCRIPT_NAME']) === $scriptName)
				$this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
			else if (basename($_SERVER['PHP_SELF']) === $scriptName)
				$this->_scriptUrl = $_SERVER['PHP_SELF'];
			else if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)
				$this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
				$this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
			else if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)
				$this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
			else
				throw new Exception('CHttpRequest is unable to determine the entry script URL.');
		}
		return $this->_scriptUrl;
	}
	
	/**
	 * 返回要访问的页面
	 * @return string
	 */
	public function getRequestUri() {
		if ($this->_requestUri === null) {
			if (isset($_SERVER['HTTP_X_REWRITE_URL']))
				$this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			else if (isset($_SERVER['REQUEST_URI'])) {
				$this->_requestUri = $_SERVER['REQUEST_URI'];
				if (strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false)
					$this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {
				$this->_requestUri = $_SERVER['ORIG_PATH_INFO'];
				if (!empty($_SERVER['QUERY_STRING']))
					$this->_requestUri .= '?' . $_SERVER['QUERY_STRING'];
			} else
				throw new Exception('CHttpRequest is unable to determine the request URI.');
		}
		return $this->_requestUri;
	}
	
	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 * @return string
	 */
	public function getPathInfo() {
		if ($this->_pathInfo === null) {
			$requestUri = urldecode($this->getRequestUri());
			$scriptUrl = $this->getScriptUrl();
			$baseUrl = $this->getBaseUrl();
			if (strpos($requestUri, $scriptUrl) === 0)
				$pathInfo = substr($requestUri, strlen($scriptUrl));
			else if ($baseUrl === '' || strpos($requestUri, $baseUrl) === 0)
				$pathInfo = substr($requestUri, strlen($baseUrl));
			else if (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
				$pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
			else
				throw new Exception('CHttpRequest is unable to determine the path info of the request.');
			
			if (($pos = strpos($pathInfo, '?')) !== false)
				$pathInfo = substr($pathInfo, 0, $pos);
			$this->_pathInfo = trim($pathInfo, '/');
		}
		return $this->_pathInfo;
	}
	
	/**
	 * 设置跟路径
	 * @param boolean $absolute
	 * @return string
	 */
	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === null)
			$this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
		return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
	}
	
	/**
	 * 返回当前运行脚本所在的服务器的主机名。如果脚本运行于虚拟主机中，该名称是由那个虚拟主机所设置的值决定
	 */
	public function getServerName() {
		return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
	}
	
	/**
	 * @return int
	 */
	public function getServerPort() {
		if ($this->_port === null) {
			$_default = $this->isSecure() ? 443 : 80;
			$this->_port = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : $_default;
		}
		return $this->_port;
	}
	
	/**
	 * @param int $port
	 */
	public function setServerPort($port) {
		$this->_port = (int) $port;
	}
	
	/**
	 * 返回浏览当前页面的用户的 IP 地址
	 */
	public function getRemoteAddr() {
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
	}
	
	/**
	 * 返回浏览当前页面的用户的主机名。DNS 反向解析不依赖于用户的 REMOTE_ADDR
	 */
	public function getRemoteHost() {
		return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
	}
	
	/**
	 * 获得请求的方法
	 */
	public function getRequestMethod() {
		return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'POST';
	}
	
	/**
	 * Returns a boolean indicating whether this request was made using a
	 * secure channel, such as HTTPS.
	 * @return Boolean
	 */
	public function isSecure() {
		return isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on');
	}
	
	/**
	 * 返回该请求是否为ajax请求
	 * @return Boolean
	 */
	public function getIsAjaxRequest() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
	
	/**
	 * 返回浏览器发送Referer请求头，可以让服务器了解和追踪发出本次请求的起源URL地址
	 * @return string or null 
	 */
	public function getUrlReferer() {
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
	}
	
	/**
	 * 获得用户机器上连接到 Web 服务器所使用的端口号
	 * @return number or null
	 */
	public function getRemotePort() {
		return isset($_SERVER['REMOTE_PORT']) ? (int) $_SERVER['REMOTE_PORT'] : null;
	}
	
	/**
	 * 返回User-Agent头字段用于指定浏览器或者其他客户端程序的类型和名字
	 * 如果客户机是一种无线手持终端，就返回一个WML文件；如果发现客户端是一种普通浏览器，
	 * 则返回通常的HTML文件
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	
	/**
	 * 返回当前请求头中 Accept: 项的内容，
	 * Accept头字段用于指出客户端程序能够处理的MIME类型，例如 text/html,image/*
	 * 
	 * @return array
	 */
	public function getAcceptTypes() {
		if (isset($_SERVER['HTTP_ACCEPT']))
			return explode(',', $_SERVER['HTTP_ACCEPT']);
		return null;
	}
	
	/**
	 * 返回客户端程序可以能够进行解码的数据编码方式，这里的编码方式通常指某种压缩方式
	 * 
	 * @return array or null
	 */
	public function getAcceptCharset() {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			return explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']);
		return null;
	}
	
	/**
	 * 返回客户端程序期望服务器返回哪个国家的语言文档 
	 * Accept-Language: en-us,zh-cn
	 * 
	 * @return multitype:|NULL
	 */
	public function getAcceptLanguage() {
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$_language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$this->_language = $_language[0] ? $_language[0] : 'zh-cn';
		}
		return $this->_language;
	}

}