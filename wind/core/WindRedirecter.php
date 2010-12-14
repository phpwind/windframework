<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindRedirecter {
	private $url;
	private $urlArgs;
	
	public function __construct($url = '', $urlArgs = '') {
		$this->url = $url;
		$this->urlArgs = $urlArgs;
	}
	
	/**
	 * 设置跳转链接
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 * 设置跳转链接的参数信息
	 * @param string $args
	 */
	public function setUrlArgs($args) {
		if (is_string($args) && $args != '') {
			$args = trim($args);
		} elseif (is_array($args) && count($args) > 0) {
			$_tmp = '&';
			foreach ($args as $key => $value) {
				$_tmp .= $key . '=' . urlencode(trim($value)) . '&';
			}
			$args = trim($_tmp, '&');
		}
		$this->urlArgs = $args;
	}
	
	private function buildUrl($callback = '') {

	}
	
	/**
	 * @return the $redirect
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * @return the $redirectArgs
	 */
	public function getUrlArgs() {
		return $this->urlArgs;
	}

}