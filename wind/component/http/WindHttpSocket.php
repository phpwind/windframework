<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.http.base.WindHttp');
/**
 * socket操作
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindHttpSocket extends WindHttp {
	
	private $host = '';
	private $port = 0;
	private $path = '';
	private $query = '';
	
	protected function __construct($url = '', $timeout = 5) {
		parent::__construct($url, $timeout);
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#open()
	 */
	public function open() {
		if (null === $this->httpResource) {
			$url = parse_url($this->url);
			$this->host = $url['host'];
			$this->port = isset($url['port']) && $url['port'] ? $url['port'] : 80;
			$this->path = isset($url['path']) && $url['path'] ? $url['path'] : '/';
			$this->path .= $url['query'] ? '?' . $url['query'] : '';
			$this->query = $url['query'];
			$this->httpResource = fsockopen($this->host, $this->port, &$this->eno, &$this->err, $this->timeout);
		}
		return $this->httpResource;
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#request()
	 */
	public function request($name, $value = null) {
		return fputs($this->httpResource, ($value ? $name . ': ' . $value : $name) . "\n");
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#requestByArray()
	 */
	public function requestByArray($request = array()) {
		$_request = '';
		foreach ($request as $key => $value) {
			if (is_string($key)) {
				$_request .= $key . ': ' . $value;
			}
			if (is_int($key)) {
				$_request .= $value;
			}
			$_request .= "\n";
		}
		fputs($this->httpResource, $_request);
	}
	/* 
	 * @see wind/component/http/base/WindHttp#resonseLine()
	 */
	public function response() {
		$response = '';
		while (!feof($this->httpResource)) {
			$response .= fgets($this->httpResource);
		}
		return $response;
	}
	
	/**
	 *  @see wind/component/http/base/WindHttp#response()
	 */
	public function resonseLine(){
		return feof($this->httpResource) ? '' : fgets($this->httpResource);
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#close()
	 */
	public function close() {
		if ($this->httpResource) {
			fclose($this->httpResource);
			$this->httpResource = null;
		}
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#getError()
	 */
	public function getError() {
		return $this->err ? $this->eno . ':' . $this->err : '';
	}
	/* 
	 * @see wind/component/http/base/WindHttp#post()
	 */
	public function post($url = '', $data = array(), $header = array(), $cookie = array(), $option = array()) {
		$url && $this->setUrl($url);
		$header && is_array($header) && $this->setHeaders($header);
		$cookie && is_array($cookie) && $this->setCookies($cookie);
		$data && is_array($data) && $this->setDatas($data);
		return $this->send(self::POST, $option);
	}
	/* 
	 * @see wind/component/http/base/WindHttp#get()
	 */
	public function get($url = '', $data = array(), $header = array(), $cookie = array(), $option = array()) {
		$url && $this->setUrl($url);
		$header && is_array($header) && $this->setHeaders($header);
		$cookie && is_array($cookie) && $this->setCookies($cookie);
		$data && is_array($data) && $this->setDatas($data);
		return $this->send(self::GET, $option);
	}
	/* 
	 * @see wind/component/http/base/WindHttp#send()
	 */
	public function send($method = self::GET, $options = array()) {
		if (self::GET === $method && $this->data) {
			$url = parse_url($this->url);
			$get = self::buildQuery($this->data, '&');
			$this->url .= ($url['query'] ? '&' : '?') . $get;
		}
		$this->open();
		$this->setHeader("Host", $this->host);
		$this->setHeader('User-Agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)');
		if ($this->cookie && $this->cookie) {
			$this->setHeader("Cookie", self::buildQuery($this->cookie, ';'));
		}
		if (self::POST === $method && $this->data) {
			$data = self::buildQuery($this->data, '&');
			$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
			$this->setHeader('Content-Length', strlen($data));
		}
		if ($options) {
			$this->setHeaders($options);
		}
		$this->setHeader('Connection', 'Close');
		$this->request($method . " " . $this->path . " HTTP/1.1");
		$this->requestByArray($this->header);
		if ($data) {
			$this->request("\n" . $data);
		}
		$this->request("\n");
		return $this->response();
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#getInstance()
	 */
	public static function getInstance($url = '') {
		if (null === self::$instance || false === (self::$instance instanceof self)) {
			self::$instance = new self($url);
		}
		return self::$instance;
	}
	
	public function __destruct() {
		$this->close();
	}
}