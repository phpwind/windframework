<?php
Wind::import('WIND:http.transfer.AbstractWindHttp');
/**
 * Enter description here ...
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package http
 * @subpackage transfer
 */
final class WindHttpStream extends AbstractWindHttp {
	/**
	 * @var string 字节流对象
	 */
	private $context = null;
	/**
	 * @var string 通信协议
	 */
	private $wrapper = 'http';

	/**
	 * 设置通信协议
	 * @param string $wrapper
	 */
	public function setWrapper($wrapper = 'http') {
		$this->wrapper = $wrapper;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::createHttpHandler()
	 */
	protected function createHttpHandler() {
		return fopen($this->url, 'r', false, $this->context);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::request()
	 */
	public function request($name, $value = null) {
		return stream_context_set_option($this->context, $this->wrapper, $name, $value);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::response()
	 */
	public function response() {
		$response = '';
		while (!feof($this->getHttpHandler())) {
			$response .= fgets($this->getHttpHandler());
		}
		return $response;
	}

	/**
	 * 释放资源
	 */
	public function close() {
		if ($this->httpHandler) {
			fclose($this->httpHandler);
			$this->httpHandler = null;
			$this->context = null;
		}
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::getError()
	 */
	public function getError() {
		return $this->err ? $this->eno . ':' . $this->err : '';
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::send()
	 */
	public function send($method = self::GET, $options = array()) {
		switch (strtoupper($method)) {
			case self::GET:
				if ($this->data) {
					$_url = WindUrlHelper::argsToUrl($this->data);
					$url = parse_url($this->url);
					$data = (isset($url['query']) ? $url['query'] . '&' : '?') . $_url;
				}
				break;
			case self::POST:
				if ($this->data) {
					$data = WindUrlHelper::argsToUrl($this->data, false);
					$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
					$this->setHeader('Content-Length', strlen($data));
				}
				break;
			default:
				break;
		}
		
		$this->setHeader("Host", $url['host']);
		$this->setHeader('User-Agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)');
		$this->setHeader('Connection', 'Close');
		if ($this->cookie) {
			$_cookie = WindUrlHelper::argsToUrl($this->cookie, false, ';=');
			$this->setHeader("Cookie", $_cookie);
		}
		if ($options) $this->setHeader($options);
		if ($this->header) {
			$header = '';
			foreach ($this->header as $key => $value) {
				$header .= $key . ': ' . $value . "\n";
			}
			$this->request('header', $header);
		}
		$this->request('method', $method);
		$this->request('timeout', $this->timeout);
		$data && $this->request('content', $data);
		$this->open();
		return $this->response();
	}
}