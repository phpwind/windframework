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
class WindUrlManager {
	private $url;
	private $urlArgs;
	
	public function __construct($url = '', $urlArgs = '') {
		$this->setUrl($url);
		$this->setUrlArgs($urlArgs);
	}
	
	public function buildUrl($callback = array(), $args = array()) {
		if (!$this->url && $callback) $this->url = call_user_func_array($callback, $args);
		$this->buildUrlArgs();
		if ($this->urlArgs) $this->url .= '&' . $this->urlArgs;
		return $this->url;
	}
	
	/**
	 * 组装URL参数信息
	 */
	private function buildUrlArgs() {
		if (!$this->urlArgs) return;
		if (is_string($this->urlArgs)) {
			$this->urlArgs = trim($this->urlArgs, ' &');
		} elseif (is_array($this->urlArgs)) {
			$_tmp = '';
			foreach ($this->urlArgs as $key => $value) {
				$_tmp .= $key . '=' . urlencode(trim($value)) . '&';
			}
			$this->urlArgs = trim($_tmp, '&');
		}
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
		$this->urlArgs = $args;
	}
}