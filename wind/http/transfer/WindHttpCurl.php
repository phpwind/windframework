<?php
Wind::import('WIND:http.transfer.AbstractWindHttp');
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id$
 * @package http
 * @subpackage transfer
 */
final class WindHttpCurl extends AbstractWindHttp {
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::createHttpHandler()
	 */
	protected function createHttpHandler() {
		return curl_init();
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::request()
	 */
	public function request($name, $value = null) {
		return curl_setopt($this->getHttpHandler(), $name, $value);
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::response()
	 */
	public function response() {
		return curl_exec($this->getHttpHandler());
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::close()
	 */
	public function close() {
		if (null === $this->httpHandler) return;
		curl_close($this->httpHandler);
		$this->httpHandler = null;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::getError()
	 */
	public function getError() {
		$this->err = curl_error($this->getHttpHandler());
		$this->eno = curl_errno($this->getHttpHandler());
		return $this->err ? $this->eno . ':' . $this->err : '';
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindHttp::send()
	 */
	public function send($method = 'GET', $options = array()) {
		if ($this->data) {
			switch (strtoupper($method)) {
				case 'GET':
					$_url = WindUrlHelper::argsToUrl($this->data);
					$url = parse_url($this->url);
					$this->url .= (isset($url['query']) ? '&' : '?') . $_url;
					break;
				case 'POST':
					$this->request(CURLOPT_POST, 1);
					$_url = WindUrlHelper::argsToUrl($this->data, false);
					$this->request(CURLOPT_POSTFIELDS, $_url);
					break;
				default:
					break;
			}
		}
		
		$this->request(CURLOPT_HEADER, 0);
		$this->request(CURLOPT_FOLLOWLOCATION, 1);
		$this->request(CURLOPT_RETURNTRANSFER, 1);
		$this->request(CURLOPT_TIMEOUT, $this->timeout);
		if ($options && is_array($options)) {
			curl_setopt_array($this->getHttpHandler(), $options);
		}
		$_cookie = '';
		foreach ($this->cookie as $key => $value) {
			$_cookie .= ($_cookie !== '' ? "" : "; ") . $key . "=" . $value;
		}
		$this->request(CURLOPT_COOKIE, $_cookie);
		
		$this->setHeader('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)', 'User-Agent');
		$_header = array();
		foreach ($this->header as $key => $value) {
			$_header[] = $key . ": " . $value;
		}
		$_header && $this->request(CURLOPT_HTTPHEADER, $_header);
		$this->request(CURLOPT_URL, $this->url);
		return $this->response();
	}
}

