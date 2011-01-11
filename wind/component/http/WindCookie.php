<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * cookie设置操作
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindCookie{
	
	/**
	 * 设置cookie
	 * @param string $name cookie名称
	 * @param string $value cookie值
	 * @param string|int $expires 过期时间
	 * @param string $path cookie路径
	 * @param strint $domain cookie cookie域
	 * @param boolean $encode 使用 MIME base64 对数据进行编码
	 * @param boolean $serialize 是否序列化
	 * @param string $prefix cookie前缀
	 * @param boolean $secure 是否安全连接
	 * @param boolean $httponly 是否可以访问脚本设置的cookie
	 * @return string|string
	 */
	public static function set($name, $value=null, $expires = null,$encode = false,$serialize = false,$prefix=null ,$path = null,$domain =null,$secure = false,$httponly=false){
		if(empty($name)){
			return false;
		}
		$name = $prefix ? $prefix.$name : $name;
		$value = $serialize ? serialize($value) : $value;
		$value = $encode ? base64_encode($value) : $value;
		$path = $path ? $path : '/';
		$expires = is_int($expires) ? time()+$expires : strtotime($expires);
		setcookie($name,$value,$expires,$path,$domain,$secure,$httponly);
		return true;
	}
	
	
	/**
	 * 删除cookie
	 * @param string $name cookie名称
	 * @param string $prefix cookie前缀
	 * @return boolean
	 */
	public static function remove($name,$prefix=null){
		 $name = $prefix ? $prefix.$name : $name;
		 if(self::exist($name)){
		 	self::set($name,'',time()-3600);
		 	unset($_COOKIE[$name]);
		 } 
		 return true;
	}
	
	/**
	 * 取得指定名称的cookie
	 * @param string $name cookie名称
	 * @param boolean $encode 是否对cookie值进行过转码
	 * @param boolean $encode 是否对cookie值进行过序列化
	 * @param string $prefix cookie前缀
	 * @return string|boolean
	 */
	public static function get($name,$encode = false,$serialize = false,$prefix=null){
		$name = $prefix ? $prefix.$name : $name;
		if(self::exist($name)){
			$value = get_magic_quotes_gpc() ? stripslashes($_COOKIE[$name]) : $_COOKIE[$name]; 
			$value = $encode ?  base64_decode($value):$value;
			return $serialize ? unserialize($value) : $value;
		}
		return false;
	}
	
	/**
	 *移除全部cookie
	 */
	public static function removeAll(){
		$_COOKIE = array();
	}
	
	/**
	 * 判断cookie是否存在
	 * @param string $name cookie名称
	 * @param string $prefix cookie前缀
	 */
	public static function exist($name,$prefix=null){
		return isset($_COOKIE[$prefix ? $prefix.$name : $name]);
	}
}