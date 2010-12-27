<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindHttpStream extends WindHttp {
	const HTTP = 'http';
	const HTTPS = 'https';
	const FTP = 'ftp';
	const FTPS = 'ftp';
	const SOCKET = 'socket';
	
	private $context = null;
	private $wrapper = self::HTTP;
	private $request = array();
	protected function __construct($url = '', $timeout = 5) {
		parent::__construct($url, $timeout);
		$this->context = stream_context_create();
	}
	
	public function setWrapper($wrapper = self::HTTP) {
		$this->wrapper = $wrapper;
	}
	
	public function open() {
		if (null === $this->httpResource) {
			$this->httpResource = fopen($this->url, 'r', false, $this->context);
		}
		return $this->httpResource;
	}
	
	public function request($name, $value = null) {
		return stream_context_set_option($this->context, $this->wrapper, $name, $value);
	}
	
	public function requestByArray($opt = array()) {
		foreach ($opt as $key => $value) {
			if (false === $this->request($key, $value)) {
				return false;
			}
		}
		return true;
	}
	
	public function response() {
		$response = '';
		while (!feof($this->httpResource)) {
			$response .= fgets($this->httpResource);
		}
		return $response;
	}
	
	public function close() {
		if ($this->httpResource) {
			fclose($this->httpResource);
			$this->httpResource = null;
			$this->context = null;
		}
	}
	
	public function getError() {
		return $this->err ? $this->eno . ':' . $this->err : '';
	}
	
	public function post($url = '', $data = array(), $header = array(), $cookie = array(), $option = array()) {
		$url && $this->setUrl($url);
		$header && is_array($header) && $this->setHeaders($header);
		$cookie && is_array($cookie) && $this->setCookies($cookie);
		$data && is_array($data) && $this->setDatas($data);
		return $this->send(self::POST, $timeout, $option);
	}
	public function get($url = '', $data = array(), $header = array(), $cookie = array(), $option = array()) {
		$url && $this->setUrl($url);
		$header && is_array($header) && $this->setHeaders($header);
		$cookie && is_array($cookie) && $this->setCookies($cookie);
		$data && is_array($data) && $this->setDatas($data);
		return $this->send(self::GET, $option);
	}
	public function send($method = self::GET, $options = array()) {
		$url = parse_url($this->url);
		if (self::GET === $method && $this->data) {
			$get = self::buildQuery($this->data, '&');
			$this->url .= ($url['query'] ? '&' : '?') . $get;
		}
		if (self::POST === $method && $this->data) {
			$data = self::buildQuery($this->data, '&');
			$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
			$this->setHeader('Content-Length', strlen($data));
		}
		$this->setHeader("Host", $url['host']);
		$this->setHeader('User-Agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)');
		if ($this->cookie) {
			$this->setHeader("Cookie", self::buildQuery($this->cookie, ';'));
		}
		$this->setHeader('Connection', 'Close');
		$this->request('method', $method);
		$this->request('timeout', $this->timeout);
		
		if ($this->header) {
			$header = '';
			foreach ($this->header as $key => $value) {
				$header .= $key . ': ' . $value . "\n";
			}
			$this->request('header', $header);
		}
		$data && $this->request('content', $data);
		$options && is_array($options) && $this->requestByArray($options);
		$this->open();
		return $this->response();
	}
	
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