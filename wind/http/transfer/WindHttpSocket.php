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
		if (!function_exists('fsockopen')) {
			throw new WindHttpTransferException(
				'[http.transfer.WindHttpSocket.createHttpHandler] initialize fsock failed, fsockopen is not exist.');
		}
		$url = parse_url($this->url);
		$this->host = $url['host'];
		$this->port = isset($url['port']) ? $url['port'] : 80;
		$this->path = isset($url['path']) ? $url['path'] : '/';
		$this->path .= $url['query'] ? '?' . $url['query'] : '';
		$this->query = $url['query'];
		return fsockopen($this->host, $this->port, $this->eno, $this->err, $this->timeout);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::request()
	 */
	public function request($name, $value = null) {
		return fputs($this->getHttpHandler(), ($value ? $name . ': ' . $value : $name) . "\n");
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
	public function send($method = self::GET, $options = array()) {
		switch (strtoupper($method)) {
			case self::GET:
				if ($this->data) {
					$_url = WindUrlHelper::argsToUrl($this->data);
					$data = (isset($this->query) ? $this->query . '&' : '') . $_url;
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
		
		$this->setHeader("Host", $this->host);
		$this->setHeader('User-Agent', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)');
		$this->setHeader('Connection', 'Close');
		if ($this->cookie) {
			$_cookit = WindUrlHelper::argsToUrl($this->cookie, false, ';=');
			$this->setHeader("Cookie", $_cookit);
		}
		$this->setHeader($options);
		
		$_request = '';
		foreach ($this->header as $key => $value) {
			if (is_string($key)) {
				$_request .= $key . ': ' . $value;
			}
			if (is_int($key)) {
				$_request .= $value;
			}
			$_request .= "\n";
		}
		$this->request($_request);
		$this->request($method . " " . $this->path . " HTTP/1.1");
		if ($data) $this->request("\n" . $data);
		$this->request("\n");
		return $this->response();
	}
}