<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 解析http请求
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WHttpRequest_back  {
	
	public function getCookie() {

	}
	
	/**
	 * 获取 HTTP POST的值
	 * @param string $key 
	 * @return mixed
	 */
	public function getPost($key = '') {
		return $key ? $_POST[$key] : $_POST;
	}
	
	/**
	 * 获取 HTTP GET的值
	 * 
	 * @param string $key 
	 * @return mixed
	 */
	public function getGet($key = '') {
		return $key ? $_GET[$key] : $_GET;
	}
	
	/**
	 * 获取 HTTP SERVER的值
	 * 
	 * @param string $key 
	 * @return mixed
	 */
	public function getServer($key = '') {
		return $key ? $_SERVER[$key] : $_SERVER;
	}
	
	/**
	 * 获取 HTTP REQUEST的值
	 * 
	 * @param string $key 
	 * @return mixed
	 */
	public function getRequest($key = '') {
		return $key ? $_REQUEST[$key] : $_REQUEST;
	}
	
	/**
	 * 取得客户端使用的 HTTP 数据传输方法
	 * 
	 * @return string
	 */
	public function getHttpMethod() {
		return ($httpMethod = $this->getServer('REQUEST_METHOD')) ? $httpMethod : 'GET';
	}
	
	/**
	 * 取得http请求的 MIME 内容类型。
	 * 
	 * @return string
	 */
	public function getAcceptTypes() {
		return $this->getServer('HTTP_ACCEPT');
	}
	
	/**
	 * 取得客户端浏览器的原始用户代理信息。
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return $this->getServer('HTTP_USER_AGENT');
	}
	
	/**
	 *验证HTTP连接中是否使用安全套接字 (ssl安全连接)
	 *
	 *@return boolean
	 */
	public function IsSecureConnection() {
		if (isset($this->_request['IS_SSL']))
			return $this->_request['IS_SSL'];
		return $this->_request['IS_SSL'] = !strcasecmp($this->getServer('HTTPS'), 'on');
	}
	
	public function isAjaxRequest() {

	}
	
	/**
	 * 取得请求页面的URI
	 * 
	 * @return string 返回http请求页面的URI
	 */
	public function getRequestUri() {
		if (isset($this->_request['REQUEST_URI']))
			return $this->_request['REQUEST_URI'];
		$requestUri = '';
		if ($uri = $this->getServer('HTTP_X_ORIGINAL_URL')) {
			$requestUri = $uri; //IIS7+Rewrite Module
		} elseif ($uri = $this->getServer('HTTP_X_REWRITE_URL')) {
			$requestUri = $uri; //IIS6 + ISAPI Rewite
		} elseif ($uri = $this->getServer('ORIG_PATH_INFO')) {
			$requestUri = $uri . (($queryString = $this->getQuery()) ? '?' . $queryString : ''); //IIS 5.0 CGI
		} elseif ($uri = $this->getServer('REQUEST_URI')) {
			$requestUri = $uri; //nginx+apache2
		} elseif ($uri = $this->getServer('REDIRECT_URL')) {
			$requestUri = $uri; //apache2
		} else {
			$requestUri = $this->getServer('PHP_SELF') . (($queryString = $this->getQuery()) ? '?' . $queryString : '');
		}
		return $this->_request['REQUEST_URI'] = $requestUri;
	}
	
	/**
	 * 取得 HTTP请求 查询字符串
	 */
	public function getQuery() {
		return $this->getServer('QUERY_STRING');
	}
	
	/**
	 * 取得http请求当前脚本文件所在的目录
	 * @return string;
	 */
	public function getFilePath() {
		if (isset($this->_request['FILEPATH']))
			return $this->_request['FILEPATH'];
		return $this->_request['FILEPATH'] = dirname($this->getServer('SCRIPT_FILENAME'));
	}
	
	/**
	 * 取得http请求当前脚本文件的真实路径
	 * @return string
	 */
	public function getFile() {
		if (isset($this->_request['FILE']))
			return $this->_request['FILE'];
		return $this->_request['FILE'] = realpath($this->getServer('SCRIPT_FILENAME'));
	}
	
	/**
	 * 取得http请求中原始的URL
	 * @return string
	 */
	public function getRequestUrl() {
		if (isset($this->_request['REQUEST_URL']))
			return $this->_request['REQUEST_URL'];
		return $this->_request['REQUEST_URL'] = $this->getHost() . $this->getRequestUri();
	}
	
	public function getBaseUrl() {

	}
	/**
	 * 取得客户端上次请求的 URL地址
	 * @return string
	 */
	public function getReferUrl() {
		return $this->getServer('HTTP_REFERER');
	}
	
	/**
	 * 取得http请求中的当前脚本文件名
	 * @return string
	 */
	public function getScript() {
		if (isset($this->_request['SCRIPT']))
			return $this->_request['SCRIPT'];
		return $this->_request['SCRIPT'] = basename($this->getServer('SCRIPT_FILENAME'));
	}
	
	/**
	 * 取得服务器DNS
	 * @param $schema string
	 * @return string
	 */
	public function getHost($schema = '') {
		if (isset($this->_request['HOST']))
			return $this->_request['HOST'];
		$schema = $schema ? $schema : $ssl = $this->IsSecureConnection() ? 'https' : 'http';
		if ($host = $this->getUserHost()) {
			$host = $schema . '://' . $host;
		} else {
			$host = $schema . '://' . $this->getServerName();
			$port = $this->getServerPort();
			$host .= (($port != 80 && !$ssl) || ($port != 443 && $ssl)) ? ':' . $port : '';
		}
		return $this->_request['HOST'] = $host;
	
	}
	/**
	 * 取得http请求中的完整主机地址
	 * @return string
	 */
	public function getUserHost() {
		return $this->getServer('HTTP_HOST');
	}
	/**
	 * 取得http请求客户端中的IP地址
	 * @return string
	 */
	public function getUserHostAddr() {
		if (isset($this->_request['REMODE_ADDR']))
			return $this->_request['REMODE_ADDR'];
		return $this->_request['REMODE_ADDR'] = $this->getServer("HTTP_X_FORWARDED_FOR") || $this->getServer("HTTP_CLIENT_IP") || $this->getServer("REMOTE_ADDR");
	}
	/**
	 * 取得http请求中服务器端主机地址
	 * @return string
	 */
	public function getServerName() {
		return $this->getServer('SERVER_NAME');
	}
	/**
	 * 取得http请求中服务器端主机地址的端口号
	 * @return string
	 */
	public function getServerPort() {
		return $this->getServer('SERVER_PORT');
	}
	/**
	 * 获取HTTP请求的客户端的浏览器相关信息
	 * @param string $userAgent 客户端浏览器的原始用户代理信息
	 * @return array
	 */
	public function getUserBrowser($userAgent = null) {
		if (isset($this->_request['USER_BROWSER']))
			return $this->_request['USER_BROWSER'];
		return $this->_request['USER_BROWSER'] = get_browser($userAgent, true);
	}
	
	/**
	 * 返回http头信息
	 * @return  array;
	 */
	public function getHeaders() {
		if (isset($this->_request['headers']))
			return $this->_request['headers'];
		return $this->_request['headers'] = function_exists('getallheaders') ? getallheaders() : $this->getAllHeaders();
	}
	private function getAllHeaders() {
		$headers = array();
		$servers = $this->getServer();
		foreach ($servers as $key => $value) {
			$key = strtoupper($key);
			if ('HTTP_' == substr($key, 0, 5)) {
				$headers[$this->makeHeaderKey($key)] = $value;
			}
			if (in_array($key, array(
				'CONTENT_LENGTH', 
				'CONTENT_TYPE'
			))) {
				$headers[$key] = $value;
			}
			if ('PHP_AUTH_DIGEST' == $key && $atthorization = $this->getServer($key)) {
				$headers['AUTHORIZATION'] = $atthorization;
			} elseif ('PHP_AUTH_USER' == $key && $user = $this->getServer($key) && $pwd = $this->getServer('PHP_AUTH_PW')) {
				$headers['AUTHORIZATION'] = base64_encode($user . ':' . $pwd);
			}
		}
		return $headers;
	}
	private function makeHeaderKey($key) {
		$newKey = '';
		$key = str_replace('_', '-', substr(strtolower($key), 5));
		foreach (explode('-', $key) as $value) {
			$newKey .= $newKey ? '-' . ucfirst($value) : ucfirst($value);
		}
		return $newKey;
	}
}

