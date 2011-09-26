<?php
/**
 * cookie操作类
 * 
 * 使用的时候全部采用静态的方式使用该类中的所有方法:
 * <code>
 * Wind::import('WIND:http.cookie.WindCookie');
 * WindCookie::set('name', 'test');
 * </code>
 * 
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.http.cookie
 */
class WindCookie {

	/**
	 * 设置cookie
	 * 
	 * @param string $name cookie名称
	 * @param string $value cookie值,默认为null
	 * @param string|int $expires 过期时间,默认为null即会话cookie,随着会话结束将会销毁
	 * @param boolean $encode 是否使用 MIME base64 对数据进行编码,默认是false即不进行编码
	 * @param boolean $serialize 是否序列化,默认为false即不进行序列化操作
	 * @param string $prefix cookie前缀,默认为null即没有前缀
	 * @param string $path cookie保存的路径,默认为null即采用默认
	 * @param string $domain cookie所属域,默认为null即不设置
	 * @param boolean $secure 是否安全连接,默认为false即不采用安全链接
	 * @param boolean $httponly 是否可通过客户端脚本访问,默认为false即客户端脚本可以访问cookie
	 * @return boolean 设置成功返回true,失败返回false
	 */
	public static function set($name, $value = null, $expires = null, $encode = false, $serialize = false, $prefix = null, $path = null, $domain = null, $secure = false, $httponly = false) {
		if (empty($name)) {
			return false;
		}
		$name = $prefix ? $prefix . $name : $name;
		$value = $serialize ? serialize($value) : $value;
		$value = $encode ? base64_encode($value) : $value;
		$path = $path ? $path : '/';
		$expires = is_int($expires) ? time() + $expires : strtotime($expires);
		setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
		return true;
	}

	/**
	 * 根据cookie的名字删除cookie
	 * 
	 * @param string $name cookie名称
	 * @param string $prefix cookie前缀,默认为null,即没有前缀
	 * @return boolean 删除成功返回true
	 */
	public static function remove($name, $prefix = null) {
		$name = $prefix ? $prefix . $name : $name;
		if (self::exist($name)) {
			self::set($name, '', time() - 3600);
			unset($_COOKIE[$name]);
		}
		return true;
	}

	/**
	 * 取得指定名称的cookie值
	 * 
	 * @param string $name cookie名称
	 * @param boolean $dencode 是否对cookie值进行过解码,默认为false即不用解码
	 * @param boolean $unserialize 是否对cookie值进行过反序列化,默认为false即不用反序列化
	 * @param string $prefix cookie前缀,默认为null即没有前缀
	 * @return mixed 获取成功将返回保存的cookie值,获取失败将返回false
	 */
	public static function get($name, $dencode = false, $unserialize = false, $prefix = null) {
		$name = $prefix ? $prefix . $name : $name;
		if (self::exist($name)) {
			$value = get_magic_quotes_gpc() ? stripslashes($_COOKIE[$name]) : $_COOKIE[$name];
			$value = $dencode ? base64_decode($value) : $value;
			return $unserialize ? unserialize($value) : $value;
		}
		return false;
	}

	/**
	 * 移除全部cookie
	 * 
	 * @return boolean 移除成功将返回true
	 */
	public static function removeAll() {
		$_COOKIE = array();
		return true;
	}

	/**
	 * 判断cookie是否存在
	 * 
	 * @param string $name cookie名称
	 * @param string $prefix cookie前缀,默认为null即没有前缀
	 * @return boolean 如果不存在则返回false,否则返回true
	 */
	public static function exist($name, $prefix = null) {
		return isset($_COOKIE[$prefix ? $prefix . $name : $name]);
	}
}