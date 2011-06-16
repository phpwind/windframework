<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.http.transfer.AbstractWindHttp');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindHttpCurl extends AbstractWindHttp {
	
	protected function __construct($url = '', $timeout = 5) {
		parent::__construct($url, $timeout);
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#open()
	 */
	public function open() {
		if (null === $this->httpResource) {
			$this->httpResource = curl_init();
		}
		return $this->httpResource;
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#request()
	 */
	public function request($name, $value = null) {
		return curl_setopt($this->httpResource, $name, $value);
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#requestByArray()
	 */
	public function requestByArray($opt = array()) {
		return curl_setopt_array($this->httpResource, $opt);
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#response()
	 */
	public function response() {
		return curl_exec($this->httpResource);
	}
	
	/**
	 * @see wind/component/http/base/WindHttp#resonseLine()
	 */
	public function resonseLine(){
		return '';
	}
	
	/**
	 * 释放资源
	 */
	public function close() {
		if ($this->httpResource) {
			curl_close($this->httpResource);
			$this->httpResource = null;
		}
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#getError()
	 */
	public function getError() {
		$this->err = curl_error($this->httpResource);
		$this->eno = curl_errno($this->httpResource);
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
		if (null === $this->httpResource) {
			$this->open();
		}
		$this->request(CURLOPT_HEADER, 0);
		$this->request(CURLOPT_FOLLOWLOCATION, 1);
		$this->request(CURLOPT_RETURNTRANSFER, 1);
		$this->request(CURLOPT_TIMEOUT, $this->timeout);
		if ($options && is_array($options)) {
			$this->requestByArray($options);
		}
		if (self::GET === $method && $this->data) {
			$get = self::buildQuery($this->data, '&');
			$url = parse_url($this->url);
			$sep = isset($url['query']) ? '&' : '?';
			$this->url .= $sep . $get;
		}
		if (self::POST === $method && $this->data) {
			$this->request(CURLOPT_POST, 1);
			$this->request(CURLOPT_POSTFIELDS, self::buildQuery($this->data, '&'));
		}
		if ($this->cookie && $this->cookie) {
			$this->request(CURLOPT_COOKIE, self::buildQuery($this->cookie, ';'));
		}
		if (empty($this->header)) {
			$this->setHeader('User-Agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1');
		}
		$this->request(CURLOPT_HTTPHEADER, self::buildArray($this->header, ':'));
		$this->request(CURLOPT_URL, $this->url);
		return $this->response();
	}
	
	/* 
	 * @see wind/component/http/base/WindHttp#requestByArray()
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

