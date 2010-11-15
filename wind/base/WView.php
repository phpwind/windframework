<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
/**
 * 视图解析输出类
 * 该类中有相关行的配置变量集$config，期中包含有四个关键配置（暂定）：：
 * $config = array('cachePath' => '', //指向用户需要的编译缓存目录--如果是PHP引擎（及才用PHP和html的方式输出）则无需设置 
 *					'templateExt' => 'phtml',//模板使用的后缀名称，默认是phtml（暂定）
 *					'engine' => 'php', //模板使用的引擎，默认是php(暂定),用户可以根据自己的需要配置并且部署自己的引擎，如smarty
 *					'templatePath' => '',//模板文件读取的路径
 *					'charset' => '', //模板输出的字符集
 *            );
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WView {
	private $config = array ();
	private static $instance = null;
	private $viewContents = ''; //输出的内容
	private $var; //模板变量输出
	

	/**
	 * 返回该类的静态实例
	 * @param array $config //初始化配置信息
	 * @return WView $instance
	 */
	public function getInstance($config = NULL) {
		if (self::$instance == null) {
			$class = new ReflectionClass(__CLASS__);
			self::$instance = call_user_func_array(array(
				$class, 
				'newInstance'
			), array());
			self::$instance ->_initView($config);
		}
		return self::$instance;
	}
	/**
	 * 设置模板文件路径
	 * @param string $path
	 */
	public function setTemplatePath($path) {
		($path && file_exists($path)) && $this->config['templatePath'] = $path;
	}
	/**
	 * 设置模板编译缓存路径
	 * @param string $path
	 */
	public function setCachePath($path) {
		($path && file_exists($path)) && $this->config['cachePath'] = $path;
	}
	/**
	 * 设置模板使用的引擎
	 * @param string $engine
	 */
	public function setEngine($engine) {
		($engine) && $this->config['engine'] = $engine;
	}
	/**
	 * 设置模板的后缀
	 * @param string $ext
	 */
	public function setTemplateExt($ext) {
		($ext) && $this->config['templateExt'] = $ext;
	}
	//TODO 解析配置信息
	private function _initView($config) {
		if (!is_array($config)) {
			$this->config = array('cachePath' => R_P . '/cache/', 
							'templateExt' => 'phtml',
							'charset' => 'gbk',
							'engine' => 'php',
							'templatePath' => R_P . '/template/');
		} else {
			foreach ($config as $key => $value) {
				(trim($value)) && $this->config[trim($key)] = trim($value);
			}
		}
		$this->viewContent = '';
		$this->var = array();
	}
	/**
	 * 设置模板中的变量，
	 * 如果只有一个参数，并且传入的是关联数组，则拆分注册
	 * 如果传入的是一个对象，则将按照数组的格式转化该对象中的变量，
	 *      且使用的时候需：$var['类变量名']的方式来调用
	 * 如果需要保存一个对象，则应该使用方法 $this->assignByRef();来代替
	 * @param string $val 变量名字
	 * @param string $value 变量值
	 */
	public function assign($var, $value = null) {
		if (is_array($var)) {
            foreach ($var as $_key => $_val) {
               (trim($_key) != '') && $this->var[$_key] = trim($_val);
            }
		} elseif (is_object($value)) {
			$this->var[$var] = get_object_vars($value);
		} else {
			$this->var[$var] = $value;
		}
	}
	/**
	 * 设置模板中的变量为对象
	 * 使用的时候：$var->变量名的方式;输出
	 * @param string $val 变量名字
	 * @param string $value 变量值
	 */
	public function assignByRef($var, &$value) {
		(is_object($value)) && $this->var[$var] = $value;
	}
	/**
	 * 设置变量
	 * @param string $var 变量名字
	 * @param string $value 变量值
	 */
	public function __set($var, $value) {
		$this->assign($var, $value);
	}
	/**
	 * 获得模板变量
	 * @param string $var 变量名字
	 * @return string  变量的值
	 */
	public function __get($name) {
		if (isset($this->var[$name])) return $this->var[$name];
		return null;
	}
	
	/**
	 * 处理跳转
	 * @param string $url 跳转的目标url
	 * @param array $params 跳转传递的参数
	 * @param integer $delayTime 跳转延迟的时间
	 * @param string $msg 显示的信息
	 */
	//TODO url有效性判断，此处是否可以将具体的实现转移至工具类中，以便其它地方调用
	public function redirect($url, $params = array(), $delayTime = 0, $msg = '') {
		$url = str_replace(array("\n", "\r" ), '', $url);
		$parse = '';
		foreach ((array)$params as $key => $value) {
			($value != '') && $parse .= "{$key}={$value}&";
		}
		(strpos($url, '?') === false) ? $url .= "?{$parse}" : "&{$parse}";
		
		($msg == '') && $msg = "系统将在{$delayTime}秒之后，自动跳转到!";
		$delayTime = intval($delayTime);
		if (!headers_sent()) {
			if ($delayTime === 0) {
				header('Location:' . $url);
				exit();
			} else {
				header("refresh:{$delayTime}; url={$url}");
				exit($msg);
			}
		}
		$jumpStr = "<meta http-equiv='Refresh' content='{$delayTime};URL={$url}'>";
		($delayTime > 0) && $jumpStr .= $msg;
		exit($jumpStr );
	}
	
	/**
	 * 显示模板
	 * @param string $templateFile 模板名称
	 * @param string $charset 输出的字符集（默认为系统的）
	 * @param string $contentType 输出的类型
	 */
	public function display($templateFile = '', $charset = '', $contentType = '') {
		$this->fetch($templateFile, $charset, $contentType, false );
	}
	
	/**
	 * 获得模板内容
	 * @param string $templateFile 模板名称
	 * @param string $charset 输出的字符集（默认为系统的）
	 * @param string $contentType 输出的类型
	 * @param boolean $return 是否返回还是立即显示
	 */
	//TODO 获得模板内容
	public function fetch($template = '', $charset = '', $contentType = 'text/html', $return = true) {
		if ($template == '') return;
		$templateFile = $this->config['templatePath'] . $template . '.' . $this->config['templateExt'];
		
		(!$charset) && $charset = $this->config ['charset'];
		(!$contentType) && $contentType = 'text/html';
		if(!headers_sent()) {
			header("Content-Type:" . $contentType . "; charset=" . $charset);
			header("Cache-control: private"); //支持页面回跳
		}
		(extension_loaded('zlib')) ? ob_start('ob_gzhandler') : ob_start();
		switch (strtolower($this->config['engine'])) {
			case 'php':
				extract($this->var, EXTR_OVERWRITE );
				if (!file_exists($templateFile) || !is_readable($templateFile)) return 'ERR_TEMPLATE:' . $templateFile;
				include $templateFile;
				$this->viewContent = ob_get_contents();
				break;
			case 'phpwind':
			default:
				$tmplangfile2 = $this->config['cachePath'] . $template . '.' . $this->config['templateExt'];;
				$this->viewContent = WTemplate::fetch($templateFile, $tmplangfile2, $this->var);
				echo $this->viewContent;
				break;
		}
		if ($return) {
			ob_end_clean();
			return $this->viewContent;
		} else {
			ob_end_flush();
		}
	}
	/**
	 * 处理 ajax请求的返回信息显示
	 * @param mixed $data  显示的数据
	 * @param string $type  返回的类型，默认为JSON类型
	 */
	public function ajaxReturn($data='', $type='JSON') {
		(!$data) && $data = $this->var;
		$type = strtoupper(trim($type));
		switch ($type) {
			case 'JSON':
				header("Content-Type:text/html; charset=utf-8");
				if (is_array($data)) $data = json_encode($data);
				elseif (is_object($data)) $data = json_encode(get_object_vars($data));
				exit($data);
				break;
			case 'XML':				
				header("Content-Type:application/xml; charset=utf-8");
				//TODO xml解析输出
				exit(WView::xml_encode($data));
				break;
			case 'HTML':
				header("Content-Type:text/html; charset=utf-8");
				exit(serialize($data));
				break;
			default:
				exit($data);
		}
	}
	
	/**
	 * 将数据组装成正确的xml输出
	 * @param mixed $data 需要解析的数据
	 * @return string 解析后的数据
	 */
	//TODO 解析xml考虑放在全局类库中
	public function xml_encode($data) {
		$xml = new DOMDocument();
	    $xml->formatOutput = true;
	    $root = $xml->createElement('phpwind');
	    $xml->appendChild(WView::data_format($xml, $root, $data));
		echo $xml->saveXML();
	}
	/**
	 * 根据数据生成xml各个节点
	 * 如果传入的不是数组：
	 *    则生成的但节点名字统一为item
	 * 如果传入的是对象：
	 *    转化为数组。
	 * 如果传入的是数组：
	 *    关联数组：则以键名为节点，以值为文本节点
	 *    数字数组：则以item-$key为节点，以值为文本节点
	 * @param DOMDocument $xml;
	 * @param DOMElement $root 父节点
	 * @param mixed $data 数据
	 */
	//解析xml
	public function data_format(&$xml, &$root, $data) {
		if (is_object($data)) $data = get_object_vars($data);
		if (!is_array($data)) {
			$note = $xml->createElement('item');
			$note->appendChild($xml->createTextNode($data));
			$root->appendChild($note);
			return $root;
		}
		foreach ($data as $key => $value) {
			(is_numeric($key)) && $key = 'item-' . $key;
			$note = $xml->createElement($key);
			(!is_array($value)) ? $note->appendChild($xml->createTextNode($value)) : $note = WView::data_format($xml, $note, $value);
			$root->appendChild($note);
		}
		return $root;
	}
}