<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package http
 * @subpackage transfer
 */
abstract class AbstractWindHttp {
	const GET = 'GET';
	const POST = 'POST';
	/**  
	 * 发送的cookie
	 * 
	 * @var string   
	 */
	protected $cookie = array();
	/**  
	 * 发送的http头 
	 * 
	 * @var array   
	 */
	protected $header = array();
	/**  
	 * 发送的数据  
	 * 
	 * @var array
	 */
	protected $data = array();
	/**
	 * 错误信息
	 * 
	 * @var string
	 */
	protected $err = '';
	/**
	 * 错误编码
	 * 
	 * @var string
	 */
	protected $eno = 0;
	
	/**
	 * 超时时间
	 * 
	 * @var string
	 */
	protected $timeout = 0;
	/**  
	 * 访问的URL地址 
	 * 
	 * @var array
	 */
	protected $url = '';
	
	/**
	 * http连接句柄
	 */
	protected $httpHandler = null;

	/**
	 * 声明受保护的构造函数,避免在类的外界实例化
	 * 
	 * @param string $url
	 * @param int $timeout
	 */
	public function __construct($url = '', $timeout = 5) {
		$this->url = $url;
		$this->timeout = $timeout;
	}

	/**
	 * 发送请求底层操作
	 * 
	 * @param string $method 请求方式
	 * @param array $options 额外的主求参数
	 * @return string 返回页根据请求的响应页面
	 */
	abstract public function send($method = self::GET, $options = array());

	/**
	 * 发送post请求
	 * 
	 * @param array $data 请求的数据
	 * @param array $header 发送请求的头
	 * @param array $cookie  发送的cookie
	 * @param array $options 额外的请求头
	 * @return string 返回页根据请求的响应页面
	 */
	public function post($data = array(), $header = array(), $cookie = array(), $options = array()) {
		$this->setHeader($header);
		$this->setCookie($cookie);
		$this->setData($data);
		return $this->send(self::POST, $options);
	}

	/**
	 * get方式传值
	 * 
	 * @param array $data 请求的数据
	 * @param array $header 发送请求的头
	 * @param array $cookie  发送的cookie
	 * @param array $options 额外的请求头
	 * @return string 返回页根据请求的响应页面
	 */
	public function get($data = array(), $header = array(), $cookie = array(), $options = array()) {
		$this->setHeader($header);
		$this->setCookie($cookie);
		$this->setData($data);
		return $this->send(self::GET, $options);
	}

	/**
	 * 发送请求
	 * 
	 * @param string $key  请求的名称
	 * @param string $value 请求的值
	 * @return boolean
	 */
	abstract public function request($key, $value = null);

	/**
	 * 响应用户的请求
	 * 
	 * @return string 返回响应
	 */
	abstract public function response();

	/**
	 * 创建http链接句柄并返回
	 * 
	 * @return handler 返回链接句柄
	 */
	abstract protected function createHttpHandler();

	/**
	 * 取得http通信中的错误
	 */
	abstract public function getError();

	/**
	 * 关闭请求
	 * 
	 * @return boolean
	 */
	abstract public function close();

	/**
	 * 打开一个http请求,返回 http请求句柄
	 * 
	 * @return httpResource
	 */
	protected function getHttpHandler() {
		if (null === $this->httpHandler) {
			$this->httpHandler = $this->createHttpHandler();
		}
		return $this->httpHandler;
	}

	/**
	 * 清理链接
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * 设置http头,支持单个值设置和批量设置
	 * 
	 * @param string|array $key
	 * @param string $value
	 * @return void
	 */
	public function setHeader($key, $value = null) {
		if (!$key) return;
		if (is_array($key))
			$this->header = array_merge($this->header, $key);
		else
			$this->header[$key] = $value;
	}

	/**
	 * 设置cookie,支持单个值设置和批量设置
	 * 
	 * @param string|array $key
	 * @param string $value
	 */
	public function setCookie($key, $value = null) {
		if (!$key) return;
		if (is_array($key))
			$this->cookie = array_merge($this->cookie, $key);
		else
			$this->cookie[$key] = $value;
	}

	/**
	 * 设置data,支持单个值设置和批量设置
	 * 
	 * @param string|array $key
	 * @param string $value
	 */
	public function setData($key, $value = null) {
		if (!$key) return;
		if (is_array($key))
			$this->data = array_merge($this->data, $key);
		else
			$this->data[$key] = $value;
	}
}