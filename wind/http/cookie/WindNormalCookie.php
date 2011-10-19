<?php
Wind::import('WIND:http.IWindHttpContainer');
/**
 * 将cookie作为对象操作
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package http
 * @subpackage cookie
 */
Wind::import('WIND:utility.WindCookie');
class WindNormalCookie extends WindModule implements IWindHttpContainer{
	protected $prefix = null;
	protected $encode = false;
	protected $expires = null;
	protected $path = null;
	protected $domain = null;
	protected $secure = false;
	protected $httponly = false;

	/**
	 * 构造函数
	 * 
	 * 根据传入的cookie数据初始化cookie数据
	 * 
	 * @param string|int $expires 过期时间,默认为null即会话cookie,随着会话结束将会销毁
	 * @param boolean $encode 是否使用 MIME base64 对数据进行编码,默认是false即不进行编码
	 * @param string $prefix cookie前缀,默认为null即没有前缀
	 * @param string $path cookie保存的路径,默认为null即采用默认
	 * @param string $domain cookie所属域,默认为null即不设置
	 * @param boolean $secure 是否安全连接,默认为false即不采用安全链接
	 * @param boolean $httponly 是否可通过客户端脚本访问,默认为false即客户端脚本可以访问cookie
	 * @return void
	 */
	public function __construct($prefix = null, $encode = false, $expires = null, $path = null, $domain = null, $secure = false, $httponly = false) {
		$this->prefix = $prefix;
		$this->encode = $encode;
		$this->expires = $expires;
		$this->domain = $domain;
		$this->path = $path;
		$this->secure = $secure;
		$this->httponly = $httponly;
	}

	/**
	 * 配置设置
	 *
	 * @param array|string $config
	 * @see WindModule::setConfig()
	 * @return void
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->prefix = $this->getConfig('prefix');
		$this->encode = $this->getConfig('encode');
		$this->expires = $this->getConfig('expires');
		$this->domain = $this->getConfig('domain');
		$this->path = $this->getConfig('path');
		$this->secure = $this->getConfig('secure');
		$this->httponly = $this->getConfig('httponly');
	}

	/**
	 * 设置cookie
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return boolean
	 */
	public function set($name, $value = null) {
		return WindCookie::set($name, $value, $this->prefix, $this->encode, $this->expires, $this->path, $this->domain, 
			$this->secure, $this->httponly);
	}

	/**
	 * 获取cookie值
	 *
	 * @param string $name
	 * @return void
	 */
	public function get($name) {
		return WindCookie::get($name, $this->prefix, $this->encode);
	}

	/**
	 * 移除cookie值
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function delete($name) {
		return WindCookie::delete($name, $this->prefix);
	}

	/**
	 * 移除全部cookie值
	 * 
	 * @return boolean
	 */
	public function deleteAll() {
		return WindCookie::deleteAll();
	}

	/**
	 * 判断cookie值是否存在
	 *
	 * @param string $name
	 */
	public function exist($name) {
		return WindCookie::exist($name, $this->prefix);
	}

	/**
	 * 获取cookie的名称
	 * 
	 * @return string 获得cookie的名字
	 */
	public function getName() {
		return $this->prefix ? $this->prefix . $this->name : $this->prefix;
	}

	/**
	 * 获取cookie值
	 * 
	 * @return string 获得cookie值
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * 获取cookie的域
	 * 
	 * @return string 获得cookie域
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * 获取cookie的路径
	 * 
	 * @return string 获得cookie保存路径
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * 获取cookie的过期时间
	 * 
	 * @return mixed 获得cookie的过期时间
	 */
	public function getExpirs() {
		return $this->expires;
	}
}