<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class WView {
	private $config = array ();
	private static $instance = null;
	private $viewContents = ''; //输出的内容
	private $var; //模板变量输出
	

	//TODO 试图配置信息解析
	public function getInstance($config = NULL) {
		if (self::$instance == null) {
			$class = new ReflectionClass(__CLASS__);
			$args = func_get_args();
			self::$instance = call_user_func_array(array(
				$class, 
				'newInstance'
			), array());
			self::$instance ->_initView ( $config );
		}
		return self::$instance;
	}
	//TODO
	private function _initView($config) {
		if (! $config)
			$config = array ('engine' => 'php', 'cache_path' => '', 'tmpExt' => 'phtml' );
		$this->config = $config;
	}
	public function assign($val, $value) {
		if (is_object ( $val )) {
			$this->var [$val] = get_object_vals ( $val );
		} else {
			$this->var [$val] = $value;
		}
	}
	public function __set($val, $value) {
		$this->assign ( $val, $value );
	}
	public function __get($val) {
		if (isset ( $this->val [$val] ))
			return $this->val [$val];
		return null;
	}
	
	//TODO 有效性判断，此处是否可以将具体的实现转移至工具类中，以便其它地方调用
	public function redirect($url, $params = array(), $delayTime = 0, $msg = '') {
		$url = str_replace ( array ("\n", "\r" ), '', $url );
		$parse = '';
		foreach ( ( array ) $params as $key => $value ) {
			if ($value != '')
				$parse .= "{$key}={$value}&";
		}
		(strpos ( $url, '?' ) === false) ? $url .= "?{$parse}" : "&{$parse}";
		if ($msg == '') {
			$msg = "系统将在{$delayTime}秒之后，自动跳转到!";
		}
		$delayTime = intval ( $delayTime );
		if (! headers_sent ()) {
			if ($delayTime === 0) {
				header ( 'Location:' . $url );
				exit ();
			} else {
				header ( "refresh:{$delayTime};url={$url}" );
				exit ( $msg );
			}
		}
		$jumpStr = "<meta http-equiv='Refresh' content='{$delayTime};URL={$url}'>";
		($delayTime > 0) && $jumpStr .= $msg;
		exit ( $jumpStr );
	}
	
	/**
	 * 
	 * @param string $templateFile 模板名称
	 * @param string $charset 输出的字符集（默认为系统的）
	 * @param string $contentType 输出的类型
	 */
	public function display($templateFile = '', $charset = '', $contentType = '') {
		$this->fetch ( $templateFile, $charset, $contentType, false );
	}
	
	/**
	 * @param string $templateFile 模板名称
	 * @param string $charset 输出的字符集（默认为系统的）
	 * @param string $contentType 输出的类型
	 * @param boolean $return 是否返回还是立即显示
	 */
	//TODO 获得模板内容
	public function fetch($templateFile = '', $charset = '', $contentType = 'text/html', $return = true) {
		$templateFile = $this->config ['cache_path'] . $templateFile . '.' . $this->config ['tmpExt'];
		if (!file_exists ( $templateFile ))
			return;
		(! $charset) && $charset = $this->config ['charset'];
		(! $contentType) && $contentType = 'text/html';
		if(!headers_sent()) {
			header ( "Content-Type:" . $contentType . "; charset=" . $charset );
			header ( "Cache-control: private" ); //支持页面回跳
		}
		if (extension_loaded ( 'zlib' ))
			ob_start ( 'ob_gzhandler' ); //开启缓存输出
		else
			ob_start ();
		extract ( $this->var, EXTR_OVERWRITE );
		include $templateFile;
		$this->viewContent = ob_get_contents ();
		if ($return) {
			ob_end_clean();
			return $this->viewContent;
		} else {
			ob_end_flush();
		}
	}
	//TODO  cache方面，同时能调用解析不同模板引擎
}