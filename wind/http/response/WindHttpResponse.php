<?php
Wind::import('WIND:http.response.IWindResponse');
/**
 * 1xx：信息，请求收到，继续处理
 * 2xx：成功，行为被成功地接受、理解和采纳
 * 3xx：重定向，为了完成请求，必须进一步执行的动作
 * 4xx：客户端错误，请求包含语法错误或者请求无法实现
 * 5xx：服务器错误，服务器不能实现一种明显无效的请求
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindHttpResponse implements IWindResponse {
	
	private $_body = array();
	
	private $_bodyIndex = array();
	
	private $_charset = 'utf-8';
	
	private $_headers = array();
	
	private $_isRedirect = false;
	
	private $_status = '';
	
	private $_data = array('G' => array(), 'F' => array());
	
	/*
     * Server status codes; see RFC 2068.
     * Status code (100) indicating the client can continue.
     */
	const W_CONTINUE = 100;
	
	/**
	 * Status code (101) indicating the server is switching protocols
	 * according to Upgrade header.
	 */
	const W_SWITCHING_PROTOCOLS = 101;
	
	/**
	 * Status code (200) indicating the request succeeded normally.
	 */
	const W_OK = 200;
	
	/**
	 * Status code (201) indicating the request succeeded and created
	 * a new resource on the server.
	 */
	const W_CREATED = 201;
	
	/**
	 * Status code (202) indicating that a request was accepted for
	 * processing, but was not completed.
	 */
	const W_ACCEPTED = 202;
	
	/**
	 * Status code (203) indicating that the meta information presented
	 * by the client did not originate from the server.
	 */
	const W_NON_AUTHORITATIVE_INFORMATION = 203;
	
	/**
	 * Status code (204) indicating that the request succeeded but that
	 * there was no new information to return.
	 */
	const W_NO_CONTENT = 204;
	
	/**
	 * Status code (205) indicating that the agent <em>SHOULD</em> reset
	 * the document view which caused the request to be sent.
	 */
	const W_RESET_CONTENT = 205;
	
	/**
	 * Status code (206) indicating that the server has fulfilled
	 * the partial GET request for the resource.
	 */
	const W_PARTIAL_CONTENT = 206;
	
	/**
	 * Status code (300) indicating that the requested resource
	 * corresponds to any one of a set of representations, each with
	 * its own specific location.
	 */
	const W_MULTIPLE_CHOICES = 300;
	
	/**
	 * Status code (301) indicating that the resource has permanently
	 * moved to a new location, and that future references should use a
	 * new URI with their requests.
	 */
	const W_MOVED_PERMANENTLY = 301;
	
	/**
	 * Status code (302) indicating that the resource has temporarily
	 * moved to another location, but that future references should
	 * still use the original URI to access the resource.
	 *
	 * This definition is being retained for backwards compatibility.
	 * W_FOUND is now the preferred definition.
	 */
	const W_MOVED_TEMPORARILY = 302;
	
	/**
	 * Status code (302) indicating that the resource reside
	 * temporarily under a different URI. Since the redirection might
	 * be altered on occasion, the client should continue to use the
	 * Request-URI for future requests.(HTTP/1.1) To represent the
	 * status code (302), it is recommended to use this variable.
	 */
	const W_FOUND = 302;
	
	/**
	 * Status code (303) indicating that the response to the request
	 * can be found under a different URI.
	 */
	const W_SEE_OTHER = 303;
	
	/**
	 * Status code (304) indicating that a conditional GET operation
	 * found that the resource was available and not modified.
	 */
	const W_NOT_MODIFIED = 304;
	
	/**
	 * Status code (305) indicating that the requested resource
	 * <em>MUST</em> be accessed through the proxy given by the
	 * <code><em>Location</em></code> field.
	 */
	const W_USE_PROXY = 305;
	
	/**
	 * Status code (307) indicating that the requested resource 
	 * resides temporarily under a different URI. The temporary URI
	 * <em>SHOULD</em> be given by the <code><em>Location</em></code> 
	 * field in the response.
	 */
	const W_TEMPORARY_REDIRECT = 307;
	
	/**
	 * Status code (400) indicating the request sent by the client was
	 * syntactically incorrect.
	 */
	const W_BAD_REQUEST = 400;
	
	/**
	 * Status code (401) indicating that the request requires HTTP
	 * authentication.
	 */
	const W_UNAUTHORIZED = 401;
	
	/**
	 * Status code (402) reserved for future use.
	 */
	const W_PAYMENT_REQUIRED = 402;
	
	/**
	 * Status code (403) indicating the server understood the request
	 * but refused to fulfill it.
	 */
	const W_FORBIDDEN = 403;
	
	/**
	 * Status code (404) indicating that the requested resource is not
	 * available.
	 */
	const W_NOT_FOUND = 404;
	
	/**
	 * Status code (405) indicating that the method specified in the
	 * <code><em>Request-Line</em></code> is not allowed for the resource
	 * identified by the <code><em>Request-URI</em></code>.
	 */
	const W_METHOD_NOT_ALLOWED = 405;
	
	/**
	 * Status code (406) indicating that the resource identified by the
	 * request is only capable of generating response entities which have
	 * content characteristics not acceptable according to the accept
	 * headers sent in the request.
	 */
	const W_NOT_ACCEPTABLE = 406;
	
	/**
	 * Status code (407) indicating that the client <em>MUST</em> first
	 * authenticate itself with the proxy.
	 */
	const W_PROXY_AUTHENTICATION_REQUIRED = 407;
	
	/**
	 * Status code (408) indicating that the client did not produce a
	 * request within the time that the server was prepared to wait.
	 */
	const W_REQUEST_TIMEOUT = 408;
	
	/**
	 * Status code (409) indicating that the request could not be
	 * completed due to a conflict with the current state of the
	 * resource.
	 */
	const W_CONFLICT = 409;
	
	/**
	 * Status code (410) indicating that the resource is no longer
	 * available at the server and no forwarding address is known.
	 * This condition <em>SHOULD</em> be considered permanent.
	 */
	const W_GONE = 410;
	
	/**
	 * Status code (411) indicating that the request cannot be handled
	 * without a defined <code><em>Content-Length</em></code>.
	 */
	const W_LENGTH_REQUIRED = 411;
	
	/**
	 * Status code (412) indicating that the precondition given in one
	 * or more of the request-header fields evaluated to false when it
	 * was tested on the server.
	 */
	const W_PRECONDITION_FAILED = 412;
	
	/**
	 * Status code (413) indicating that the server is refusing to process
	 * the request because the request entity is larger than the server is
	 * willing or able to process.
	 */
	const W_REQUEST_ENTITY_TOO_LARGE = 413;
	
	/**
	 * Status code (414) indicating that the server is refusing to service
	 * the request because the <code><em>Request-URI</em></code> is longer
	 * than the server is willing to interpret.
	 */
	const W_REQUEST_URI_TOO_LONG = 414;
	
	/**
	 * Status code (415) indicating that the server is refusing to service
	 * the request because the entity of the request is in a format not
	 * supported by the requested resource for the requested method.
	 */
	const W_UNSUPPORTED_MEDIA_TYPE = 415;
	
	/**
	 * Status code (416) indicating that the server cannot serve the
	 * requested byte range.
	 */
	const W_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	
	/**
	 * Status code (417) indicating that the server could not meet the
	 * expectation given in the Expect request header.
	 */
	const W_EXPECTATION_FAILED = 417;
	
	/**
	 * Status code (500) indicating an error inside the HTTP server
	 * which prevented it from fulfilling the request.
	 */
	const W_INTERNAL_SERVER_ERROR = 500;
	
	/**
	 * Status code (501) indicating the HTTP server does not support
	 * the functionality needed to fulfill the request.
	 */
	const W_NOT_IMPLEMENTED = 501;
	
	/**
	 * Status code (502) indicating that the HTTP server received an
	 * invalid response from a server it consulted when acting as a
	 * proxy or gateway.
	 */
	const W_BAD_GATEWAY = 502;
	
	/**
	 * Status code (503) indicating that the HTTP server is
	 * temporarily overloaded, and unable to handle the request.
	 */
	const W_SERVICE_UNAVAILABLE = 503;
	
	/**
	 * Status code (504) indicating that the server did not receive
	 * a timely response from the upstream server while acting as
	 * a gateway or proxy.
	 */
	const W_GATEWAY_TIMEOUT = 504;
	
	/**
	 * Status code (505) indicating that the server does not support
	 * or refuses to support the HTTP protocol version that was used
	 * in the request message.
	 */
	const W_HTTP_VERSION_NOT_SUPPORTED = 505;

	public function codeMap($code) {
		$map = array(505 => 'http version not supported', 504 => 'gateway timeout', 503 => 'service unavailable', 
			503 => 'bad gateway', 502 => 'bad gateway', 501 => 'not implemented', 500 => 'internal server error', 
			417 => 'expectation failed', 416 => 'requested range not satisfiable', 415 => 'unsupported media type', 
			414 => 'request uri too long', 413 => 'request entity too large', 412 => 'precondition failed', 
			411 => 'length required', 410 => 'gone', 409 => 'conflict', 408 => 'request timeout', 
			407 => 'proxy authentication required', 406 => 'not acceptable', 405 => 'method not allowed', 
			404 => 'not found', 403 => 'forbidden', 402 => 'payment required', 401 => 'unauthorized', 
			400 => 'bad request', 300 => 'multiple choices', 301 => 'moved permanently', 302 => 'moved temporarily', 
			302 => 'found', 303 => 'see other', 304 => 'not modified', 305 => 'use proxy', 307 => 'temporary redirect', 
			100 => 'continue', 101 => 'witching protocols', 200 => 'ok', 201 => 'created', 202 => 'accepted', 
			203 => 'non authoritative information', 204 => 'no content', 205 => 'reset content', 
			206 => 'partial content');
		return isset($map[$code]) ? $map[$code] : '';
	}

	/**
	 * 设置响应头信息，如果已经设置过同名的响应头，该方法将用新的设置取代原来的头字段
	 * 
	 * @param string $name 响应头的名称
	 * @param string $value 响应头的字段取值
	 */
	public function setHeader($name, $value, $replace = false) {
		if (!$name || !$value)
			return;
		$name = $this->_normalizeHeader($name);
		$setted = false;
		foreach ($this->_headers as $key => $one) {
			if ($one['name'] == $name) {
				$this->_headers[$key] = array('name' => $name, 'value' => $value, 'replace' => $replace);
				$setted = true;
				break;
			}
		}
		if ($setted === false)
			$this->_headers[] = array('name' => $name, 'value' => $value, 'replace' => $replace);
	}

	/**
	 * 设置响应头信息，如果已经设置过同名的响应头，该方法将增加一个同名的响应头
	 * 
	 * @param string $name 响应头的名称
	 * @param string $value 响应头的字段取值
	 */
	public function addHeader($name, $value, $replace = false) {
		if ($name == '' || $value == '')
			return;
		$name = $this->_normalizeHeader($name);
		$this->_headers[] = array('name' => $name, 'value' => $value, 'replace' => $replace);
	}

	/**
	 * @return string
	 */
	public function getCharset() {
		return $this->_charset;
	}

	/**
	 * @param string $_charset
	 */
	public function setCharset($_charset) {
		$this->_charset = $_charset;
	}

	/**
	 * 设置响应头状态码
	 * 
	 * @param int $status
	 * @param string $message
	 */
	public function setStatus($status, $message = '') {
		$status = intval($status);
		if ($status < 100 || $status > 505)
			return;
		
		$this->_status = (int) $status;
	}

	/**
	 * 设置响应内容
	 * 
	 * @param string $content
	 * @param string $name
	 */
	public function setBody($content, $name = null) {
		if (!$content)
			return;
		!$name && $name = 'default';
		array_push($this->_bodyIndex, $name);
		$this->_body[$name] = $content;
	}

	/**
	 * 添加cookie信息
	 * 
	 * @param Cookie $cookie
	 */
	public function addCookie(Cookie $cookie) {

	}

	/**
	 * 发送一个错误的响应信息
	 * 
	 * @param int $status
	 * @param string $message
	 */
	public function sendError($status = self::W_NOT_FOUND, $message = '') {
		if (!is_int($status) || $status < 400 || $status > 505)
			return;
		$this->setBody($message, 'error');
		$this->setStatus($status);
		$this->sendResponse();
	}

	/**
	 * 重定向一个响应信息
	 * 
	 * @param string $location
	 */
	public function sendRedirect($location, $status = 302) {
		if (!is_int($status) || $status < 300 || $status > 399)
			return;
		
		$this->addHeader('Location', $location, true);
		$this->setStatus($status);
		$this->_isRedirect = true;
		$this->sendHeaders();
		exit();
	}

	/**
	 * 发送响应信息
	 */
	public function sendResponse() {
		$this->sendHeaders();
		$this->sendBody();
	}

	/**
	 * 发送响应头部信息
	 */
	public function sendHeaders() {
		if ($this->isSendedHeader())
			return;
		foreach ($this->_headers as $header) {
			header($header['name'] . ': ' . $header['value'], $header['replace']);
		}
		if ($this->_status) {
			header('HTTP/1.x ' . $this->_status . ' ' . ucwords($this->codeMap($this->_status)));
			header('Status: ' . $this->_status . ' ' . ucwords($this->codeMap($this->_status)));
		}
	}

	/**
	 * 发送响应内容
	 */
	public function sendBody() {
		/*if ($this->_isAjax) echo "<?xml version=\"1.0\" encoding=\"utf-8\"?><ajax><![CDATA[";*/
		foreach ($this->_bodyIndex as $key)
			echo $this->_body[$key];
		/*if ($this->_isAjax) echo "]]></ajax>";*/
	}

	/**
	 * 获取内容
	 * 
	 * @param string $spec 内容的名称
	 * @return string|null
	 */
	public function getBody($name = false) {
		if ($name === false) {
			ob_start();
			$this->sendBody();
			return ob_get_clean();
		} elseif ($name === true) {
			return $this->_body;
		} elseif (is_string($name) && isset($this->_body[$name]))
			return $this->_body[$name];
		
		return null;
	}

	/**
	 * 是否已经发送了响应头部
	 */
	public function isSendedHeader($throw = false) {
		$sended = headers_sent($file, $line);
		if ($throw && $sended)
			throw new WindException(__CLASS__ . ' the headers are sent in file ' . $file . ' on line ' . $line);
		
		return $sended;
	}

	/**
	 * 获取响应头信息
	 * 
	 * @return array
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * 清理响应体信息
	 */
	public function clearBody() {
		$this->_body = array();
	}

	/**
	 * 清除响应头信息
	 */
	public function clearHeaders() {
		$this->_headers = array();
	}

	/**
	 * 格式化响应头信息
	 * 
	 * @param string $name
	 * @return string
	 */
	private function _normalizeHeader($name) {
		$filtered = str_replace(array('-', '_'), ' ', (string) $name);
		$filtered = ucwords(strtolower($filtered));
		$filtered = str_replace(' ', '-', $filtered);
		return $filtered;
	}

	/**
	 * @return array
	 */
	public function getData() {
		$_tmp = $this->_data;
		foreach (func_get_args() as $arg) {
			if (is_array($_tmp) && isset($_tmp[$arg]))
				$_tmp = $_tmp[$arg];
			else
				return '';
		}
		return $_tmp;
	}

	/**
	 * @param $data
	 */
	public function setData($data, $key = '') {
		if ($key)
			$this->_data[$key] = $data;
		else {
			if (is_object($data))
				$data = get_object_vars($data);
			if (is_array($data))
				$this->_data += $data;
		}
	}

}