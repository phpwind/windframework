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
		if (!function_exists('curl_init')) {
			throw new WindHttpTransferException(
				'[http.transfer.WindHttpCurl.createHttpHandler] initialize curl failed, curl_init is not exist.');
		}
		return curl_init();
	}

	/* (non-PHPdoc)
	 * @see AbstractWindHttp::request()
	 */
	public function request($name, $value = null) {
		return curl_setopt($this->getHttpHandler(), $name, $value);
	}

	/* 
	 * @see wind/component/http/base/WindHttp#response()
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
	public function send($method = self::GET, $options = array()) {
		$this->request(CURLOPT_HEADER, 0);
		$this->request(CURLOPT_FOLLOWLOCATION, 1);
		$this->request(CURLOPT_RETURNTRANSFER, 1);
		$this->request(CURLOPT_TIMEOUT, $this->timeout);
		if ($options && is_array($options)) curl_setopt_array($this->getHttpHandler(), $options);
		
		switch (strtoupper($method)) {
			case self::GET:
				if ($this->data) {
					$_url = WindUrlHelper::argsToUrl($this->data);
					$url = parse_url($this->url);
					$this->url .= (isset($url['query']) ? '&' : '?') . $_url;
				}
				break;
			case self::POST:
				if ($this->data) {
					$this->request(CURLOPT_POST, 1);
					$_url = WindUrlHelper::argsToUrl($this->data, false);
					$this->request(CURLOPT_POSTFIELDS, $_url);
				}
				break;
			default:
				break;
		}
		if ($this->cookie) {
			$_cookie = WindUrlHelper::argsToUrl($this->cookie, false, ';=');
			$this->request(CURLOPT_COOKIE, $_cookie);
		}
		if (empty($this->header)) $this->setHeader('User-Agent', 
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1');
		$_header = WindUrlHelper::argsToUrl($this->header, false, ':=');
		$this->request(CURLOPT_HTTPHEADER, $_header);
		$this->request(CURLOPT_URL, $this->url);
		
		return $this->response();
	}
}

