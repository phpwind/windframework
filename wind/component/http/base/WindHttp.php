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
abstract class WindHttp{
	
	/**  
	 * @var WindHttp 单例 对象
	 */
	protected static $instance = null;
	
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
	protected $url = array();
	/**  
	 * @var array  发送的数据  
	 */
	protected $data = array();
	
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
	/**
	 * 声明受保护的构造函数,避免在类的外界实例化
	 * @param string $url
	 */
	protected function __construct($url = ''){
		$this->url = $url;
	}
	
	/**
	 * 获取http单例对象,对象唯一访问入口
	 * @param string $url
	 * @return WindHttp
	 */
	public static function getInstance($url ='') {
		if (null === self::$instance || false === (self::$instance instanceof self)) {
			self::$instance = new self($url);
		}
		return self::$instance;
	}
	
	/**
	 * 防止克隆
	 */
	protected  function __clone(){}
	
	/**
	 * 设置url
	 * @param string|array $url
	 */
	public  function setUrl($url){
		if(is_array($url)){
			foreach($url as $value){
				$this->url[] = $value;
			}
		}else{
			$this->url[] = $url;
		}
	}
	/**
	 * 设置http头
	 * @param string $key
	 * @param string $value
	 */
	public function setHeader($key,$value){
		$this->header[$key] = $value;
	}
	/**
	 * 批量设置http头
	 * @param array $datas 实际的http头，数组的值基于key/value形式
	 * @return boolean
	 */
	public  function setHeaders($headers=array()){
		return $this->setPropertityValue(self::_HEADER,$headers);
	}
	/**
	 * 设置cookie
	 * @param string $key
	 * @param string $value
	 */
	public function setCookie($key,$value){
		$this->cookie[$key] = $value;
	}
	/**
	 * 批量设置要传送的cookie
	 * @param array $cookies 要传送的cookie，数组的值基于key/value形式
	 * @return boolean
	 */
	public  function setCookies($cookies=array()){
		return $this->setPropertityValue(self::_COOKIE,$cookies);
	}
	/**
	 * 设置data
	 * @param string $key
	 * @param string $value
	 */
	public  function setData($key,$value){
		$this->data[$key] = $value;
	}
	/**
	 * 批量设置要传送的数据
	 * @param array $datas 要传送的数据，数组的值基于key/value形式
	 * @return boolean
	 */
	public function setDatas($datas = array()){
		return $this->setPropertityValue(self::_DATA,$datas);
	}
	public  function clear(){
		$this->url = array();
		$this->header = array();
		$this->cookie = array();
		$this->data = array();
	}
	public abstract function post();
	public abstract function get();
	public abstract function put();
	public abstract function send();
	
	/**
	 * 增量式设置对象的属性的值
	 * @param string $propertity 要设置的对象的属性
	 * @param array $value 要设置属性的值
	 * @return boolean
	 */
	private function setPropertityValue($propertity,$value = array()){
		if(!in_array($propertity,array(self::_COOKIE,self::_DATA,self::_HEADER))){
			return false;
		}
		if(!is_array($value)){
			return false;
		}
		if(empty($this->$propertity)){
			$this->$propertity = $value;
		}else{
			foreach($value as $key=>$_value){
				$this->$propertity[$key] = $_value;
			}
		}
		return true;
	}
	
	
}