<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindHttp {
	
	/**  
	 * @var WindHttp 单例 对象
	 */
	protected static $instance = null;
	
	/**
	 * @var resource http连接句柄
	 */
	protected $httpResource = null;
	
	/**  
	 * @var string 发送的cookie  
	 */
	protected $cookie = array();
	/**  
	 * @var array  发送的http头  
	 */
	protected $header = array();
	/**  
	 * @var array 访问的URL地址  
	 */
	protected $url = '';
	/**  
	 * @var array  发送的数据  
	 */
	protected $data = array();
	
	/**
	 * @var string 错误信息
	 */
	protected $err = '';
	/**
	 * @var string 错误编码
	 */
	protected $eno = 0;
	
	/**
	 * @var string 超时时间
	 */
	protected $timeout = 0;
	
	/**
	 * @var strint 指向$cookie属性
	 */
	const _COOKIE = 'cookie';
	/**
	 * @var string 指向$header属性
	 */
	const _HEADER = 'header';
	/**
	 * @var string 指定$data属性
	 */
	const _DATA = 'data';
	
	const GET = 'GET';
	const POST = 'POST';
	/**
	 * 声明受保护的构造函数,避免在类的外界实例化
	 * @param string $url
	 */
	protected function __construct($url = '', $timeout = 5) {
		$this->url = $url;
		$this->timeout = $timeout;
	}
	
	/**
	 * 发送post请求
	 * @param string $url 请求的url
	 * @param array $data 请求的数据
	 * @param array $header 发送请求的头
	 * @param array $cookie  发送的cookie
	 * @param array $options 额外的请求头
	 * @return string 返回页根据请求的响应页面
	 */
	public abstract function post($url = '', $data = array(), $header = array(), $cookie = array(), $options = array());
	/**
	 * get方式传值
	 * @param string $url 请求的url
	 * @param array $data 请求的数据
	 * @param array $header 发送请求的头
	 * @param array $cookie  发送的cookie
	 * @param array $options 额外的请求头
	 * @return string 返回页根据请求的响应页面
	 */
	public abstract function get($url = '', $data = array(), $header = array(), $cookie = array(), $options = array());
	/**
	 * 发送请求底层操作
	 * @param string $method 请求方式
	 * @param array $options 额外的主求参数
	 * @return string 返回页根据请求的响应页面
	 */
	public abstract function send($method = self::GET, $options = array());
	
	/**
	 * 打开一个http请求
	 * @return httpResource http请求指针
	 */
	public abstract function open();
	/**
	 * 发送请求
	 * @param string $key  请求的名称
	 * @param string $value 请求的值
	 * @return boolean
	 */
	public abstract function request($key, $value = null);
	/**
	 * 以数组格式请求
	 * @param array $request
	 * @return boolean
	 */
	public abstract function requestByArray($request = array());
	/**
	 * 响应用户的请求
	 * @return string 返回响应
	 */
	public abstract function response();
	/**
	 *响应用户请求，只返回一行数据
	 *@return string 
	 */
	public abstract function resonseLine();
	/**
	 * 
	 * 关闭请求
	 */
	public abstract function close();
	/**
	 * 取得http通信中的错误
	 */
	public abstract function getError();
	
	/**
	 * 获取http单例对象,对象唯一访问入口
	 * @param string $url 请求的url
	 * @return WindHttp
	 */
	public static abstract function getInstance($url = '');
	
	/**
	 * 防止克隆
	 */
	protected function __clone() {}
	
	/**
	 * 设置url
	 * @param string|array $url
	 */
	public function setUrl($url) {
		$url && $this->url = $url;
	}
	/**
	 * 设置http头
	 * @param string $key
	 * @param string $value
	 */
	public function setHeader($key, $value) {
		$this->header[$key] = $value;
	}
	/**
	 * 批量设置http头
	 * @param array $datas 实际的http头，数组的值基于key/value形式
	 * @return boolean
	 */
	public function setHeaders($headers = array()) {
		return $this->setPropertityValue(self::_HEADER, $headers);
	}
	/**
	 * 设置cookie
	 * @param string $key
	 * @param string $value
	 */
	public function setCookie($key, $value) {
		$this->cookie[$key] = $value;
	}
	/**
	 * 批量设置要传送的cookie
	 * @param array $cookies 要传送的cookie，数组的值基于key/value形式
	 * @return boolean
	 */
	public function setCookies($cookies = array()) {
		return $this->setPropertityValue(self::_COOKIE, $cookies);
	}
	/**
	 * 设置data
	 * @param string $key
	 * @param string $value
	 */
	public function setData($key, $value) {
		$this->data[$key] = $value;
	}
	/**
	 * 批量设置要传送的数据
	 * @param array $datas 要传送的数据，数组的值基于key/value形式
	 * @return boolean
	 */
	public function setDatas($datas = array()) {
		return $this->setPropertityValue(self::_DATA, $datas);
	}
	/**
	 *请空数据，重新发送请求 
	 */
	public function clear() {
		$this->url = array();
		$this->header = array();
		$this->cookie = array();
		$this->data = array();
	}
	
	/**
	 * 构请查询字符串
	 * @param array $query  查询的关联数组
	 * @param string $sep 分隔符
	 * @return string
	 */
	public static function buildQuery($query, $sep = '&') {
		if (!is_array($query)) {
			return '';
		}
		$_query = '';
		foreach ($query as $key => $value) {
			$tmp = rawurlencode($key) . '=' . rawurlencode($value);
			$_query .= $_query ? $sep . $tmp : $tmp;
		}
		return $_query;
	}
	/**
	 * 以指定分隔符的形式来将数组转化成字符串
	 * @param array $array 关联数组
	 * @param strin $sep 分隔符
	 * @return string
	 */
	public static function buildArray($array, $sep = ':') {
		if (!is_array($array)) {
			return array();
		}
		$_array = array();
		foreach ($array as $key => $value) {
			$_array[] = $key . $sep . $value;
		}
		return $_array;
	}
	
	/**
	 * 增量式设置对象的属性的值
	 * @param string $propertity 要设置的对象的属性
	 * @param array $value 要设置属性的值
	 * @return boolean
	 */
	private function setPropertityValue($propertity, $value = array()) {
		if (!in_array($propertity, array(self::_COOKIE, self::_DATA, self::_HEADER))) {
			return false;
		}
		if (!is_array($value)) {
			return false;
		}
		if (empty($this->$propertity)) {
			$this->$propertity = $value;
		} else {
			foreach ($value as $key => $_value) {
				$this->$propertity[$key] = $_value;
			}
		}
		return true;
	}
}