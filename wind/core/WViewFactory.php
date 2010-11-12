<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

class WViewFactory {
	private static $configs = array();
	private static $instance;
	private $viewContents = '';
	private $var;//模板变量输出
	
	//TODO 试图配置信息解析
	public function getInstance($config = NULL) {
		$config = array('engine' => 'php', 'cache_path' => '');
	}
	
	public function assign($val, $value) {
		if (is_object($val)) {
			$this->var[$val] = get_object_vals($val);
		} else {
			$this->var[$val] = $value;
		}
	}
	public function __set($val, $value) {
		$this->assign($val, $value);
	}
	public function __get($val) {
		if (isset($this->val[$val])) return $this->val[$val];
		return null;
	}
	
	//TODO 有效性判断，此处是否可以将具体的实现转移至工具类中，以便其它地方调用
	public function redirect($url, $params=array(), $delayTime=0, $msg='') {
		$url = str_replace(array("\n", "\r"), '', $url);
		$parse = '';
		foreach ((array)$params as $key => $value) {
			if ($value != '') $parse .= "{$key}={$value}&";
		}
		(strpos($url, '?') === false) ? $url .= "?{$parse}" : "&{$parse}";
		if ($msg == '') {
			$msg = "系统将在{$delayTime}秒之后，自动跳转到!";
		}
		$delayTime = intval($delayTime);
		if (!headers_sent()) {
			if ($delayTime === 0) {
				header('Location:' . $url);
				exit();
			} else {
				header("refresh:{$delayTime};url={$url}");
            	exit($msg);
			}
		}
		$jumpStr = "<meta http-equiv='Refresh' content='{$delayTime};URL={$url}'>";
		($delayTime > 0) && $jumpStr .= $msg;
		exit($jumpStr);
	}
	
	public function display($templateFile='', $charset='', $contentType='') {
		
	}
	
	//TODO 获得模板内容
	public function fetch($templateFile='', $charset='', $contentType='text/html', $return=true){
		
	}
	//TODO  cache方面，同时能调用解析不同模板引擎
}