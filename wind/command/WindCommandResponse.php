<?php
Wind::import('WIND:http.response.IWindResponse');
/**
 * 命令行模式的response
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $id$
 * @package command
 */
class WindCommandResponse implements IWindResponse {
	
	private $_output = '';

	/**
	 * 发送响应信息
	 * 
	 * @return void
	 */
	public function sendResponse() {
		echo $this->_output;
	}

	/**
	 * 设置输出信息
	 *
	 * @param string $output
	 */
	public function setOutput($output) {
		$this->_output = $output;
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		return array();
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::getData()
	 */
	public function getData() {
		return array();
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::setData()
	 */
	public function setData($data, $key = '') {
		return false;
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::getCharset()
	 */
	public function getCharset() {
		return '';
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::setCharset()
	 */
	public function setCharset($_charset) {
		return false;
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::setBody()
	 */
	public function setBody($content, $name = 'default') {
		return false;
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::sendError()
	 */
	public function sendError($status = self::W_NOT_FOUND, $message = '') {
		return false;
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::sendBody()
	 */
	public function sendBody() {
		return false;
	}

	/* (non-PHPdoc)
	 * @see IWindResponse::sendHeaders()
	 */
	public function sendHeaders() {
		return false;
	}

}

?>