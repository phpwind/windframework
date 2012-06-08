<?php
Wind::import('WIND:http.transfer.AbstractWindHttp');
/**
 * socket操作
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package http
 * @subpackage transfer
 */
final class WindHttpSocket extends AbstractWindHttp {
	private $host = '';
	private $port = 80;
	private $path = '';
	private $query = '';
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::createHttpHandler()
	 */
	protected function createHttpHandler() {
		$url = parse_url($this->url);
		
		$this->host = isset($url['host']) ? $url['host'] : '';
		$this->port = isset($url['port']) ? $url['port'] : 80;
		$this->path = isset($url['path']) ? $url['path'] : '/';
		$this->query = isset($url['query']) ? '?' . $url['query'] : '';
		$this->path .= $this->query;
		return fsockopen($this->host, $this->port, $this->eno, $this->err, $this->timeout);
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::request()
	 */
	public function request($name, $value = null) {
		return fputs($this->getHttpHandler(), ($value ? $name . ': ' . $value : $name));
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
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::close()
	 */
	public function close() {
		if ($this->httpHandler === null) return;
		fclose($this->httpHandler);
		$this->httpHandler = null;
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
	public function send($method = 'GET', $options = array()) {
		$method = strtoupper($method);
		if ($this->data) {
			switch ($method) {
				case 'GET':
					$_url = WindUrlHelper::argsToUrl($this->data);
					$getData = ($this->query ? $this->query . '&' : '?') . $_url;
					$this->path .= $getData;
					break;
				case 'POST':
					$postData = WindUrlHelper::argsToUrl($this->data, false);
					$this->setHeader('application/x-www-form-urlencoded', 'Content-Type');
					$this->setHeader(strlen($postData) . "\r\n\r\n" . $postData, 'Content-Length');
					break;
				default:
					break;
			}
		}
		
		$this->setHeader($method . " " . $this->path . " HTTP/1.1");
		$this->setHeader($this->host, "Host");
		$this->setHeader('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)', 'User-Agent');
		$this->setHeader('Close', 'Connection');
		if ($this->cookie) {
			$_cookit = WindUrlHelper::argsToUrl($this->cookie, false, ';=');
			$this->setHeader($_cookit, "Cookie");
		}
		$options && $this->setHeader($options);
		if (!empty($data)) $this->setHeader($data);
		
		$_request = '';
		foreach ($this->header as $key => $value) {
			if (is_string($key)) {
				$_request .= $key . ': ' . $value;
			} elseif (is_int($key)) {
				$_request .= $value;
			}
			$_request .= "\r\n";
		}
		
		$_request && $this->request($_request . "\r\n");
		return $this->response();
	}
}