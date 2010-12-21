<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * Session会话操作
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSession implements IteratorAggregate, ArrayAccess, Countable {
	/**
	 * @var boolean 是否自动启动session
	 */
	public $autostart = false;
	/**
	 * @var int 没有启用cookie传用sessionid
	 */
	const COOKIE_MODE_NONE = 1;
	/**
	 * @var int 仅仅启用cookiew传递sessionid
	 */
	const COOKIE_MODE_ONLY = 2;
	/**
	 * @var int 启用cookie传用sessionid
	 */
	const COOKIE_MODE_ALLOW = 3;
	
	/**
	 * @var string 以files格式将session在服务端的保存
	 */
	const SESSION_SAVE_FILES = 'files';
	/**
	 * @var string  以user(用户自定义)格式将session在服务端的保存
	 */
	const SESSION_SAVE_USER = 'user';
	
	/**
	 * @var array $read 只读session
	 */
	public static $read = array();
	/**
	 * @var array $write 只写session
	 */
	public static $write = array();
	
	public function __construct($autostart = false) {
		$this->autostart = $autostart;
	}
	
	public function start() {
		if (!$this->isStart() && !$this->getAutoStart()) {
			$this->autostart ? $this->setAutoStart(1) : session_start();
		}
	}
	
	/**
	 * session是否开启
	 * @return boolean
	 */
	public function isStart() {
		return '' !== $this->getSessionId();
	}
	
	/**
	 * 写入和结束session
	 */
	public function close() {
		if ($this->isStart()) {
			session_write_close();
		}
	}
	
	/**
	 * 获取session
	 * @param string $name session名称
	 * @return string
	 */
	public function get($name) {
		return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
	}
	
	/**
	 * 设置一个会话
	 * @param string $name session名称
	 * @param string $value $name对应的值
	 * @return string
	 */
	public function set($name, $value) {
		if (empty($name) && empty($value)) {
			return false;
		}
		$_SESSION[$name] = $value;
		return true;
	}
	
	/**
	 * 删除一个会话
	 * @param string $name  session名称
	 * @return string
	 */
	public function remove($name) {
		if (isset($_SESSION[$name])) {
			$sessionValue = $_SESSION[$name];
			unset($_SESSION[$name]);
			return $sessionValue;
		}
		return null;
	}
	
	/**
	 * 判断一个session是否存在
	 * @param string $name session名称
	 * @return string
	 */
	public function exist($name) {
		return isset($_SESSION[$name]);
	}
	
	/**
	 * 销毁当前所有会话
	 * @return string
	 */
	public function destroy() {
		if (($name = $this->getSessionName()) && isset($_COOKIE[$name])) {
			setcookie($name, '', time() - 3600);
		}
		session_unset();
		session_destroy();
		return true;
	}
	
	/**
	 * 获取当前会话名称
	 * @return string
	 */
	public function getSessionName() {
		return session_name();
	}
	/**
	 * 设置当前会话名称
	 * @return string
	 */
	public function setSessionName() {
		return session_name($name);
	}
	
	/**
	 * 获取当前会话 id
	 * @return string
	 */
	public function getSessionId() {
		return session_id();
	}
	
	/**
	 * 设置当前会话 id
	 * @param string $id
	 * @return string
	 */
	public function setSessionId($id) {
		return session_id();
	}
	
	/**
	 * 如果session在服务端的以files方式保存,获取session在服务器端存储路径
	 * @return string
	 */
	public function getSavePath() {
		return session_save_path();
	}
	
	/**
	 * 如果session在服务端的以files方式保存,设置session在服务器端存储路径
	 * @param string $path 
	 * @return string
	 */
	public function setSavePath($path) {
		if (is_dir($path)) {
			session_save_path($path);
			return true;
		}
		return false;
	}
	
	/**
	 * 获取session在服务端的保存方式
	 * @return string
	 */
	public function getSessionSaveMode() {
		return session_module_name();
	}
	
	/**
	 * 定义session在服务端的保存方式，files意为把sesion保存到一个临时文件里，如果我们想自定义别的方式保存（比如用数据库），则需要把该项设置为user；
	 * @param unknown_type $mode
	 * @return string
	 */
	public function setSessionSaveMode($mode = self::SESSION_SAVE_FILES) {
		return session_module_name($mode);
	}
	
	/**
	 * 取得session相关的cookie参数
	 * @return array
	 */
	public function getCookieParams() {
		return session_get_cookie_params();
	}
	
	/**
	 * 设置session相关的cookie参数
	 * @param array $cookie
	 * @return string
	 */
	public function setCookieParams($cookie = array()) {
		extract($this->getCookieParams());
		extract($cookie);
		if (isset($httponly)) {
			session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
		} else {
			session_set_cookie_params($lifetime, $path, $domain, $secure);
		}
		return true;
	}
	
	/**
	 * 取得cookie传递sessionid的模式
	 * @return number
	 */
	public function getCookieMode() {
		if ('0' === ini_get('session.use_cookies')) {
			self::COOKIE_MODE_NONE;
		} else if ('0' === ini_get('session.use_only_cookies')) {
			return self::COOKIE_MODE_ALLOW;
		} else {
			return self::COOKIE_MODE_ONLY;
		}
		return false;
	}
	
	/**
	 * 设置cookie传递sessionid的模式
	 * @param int $mode
	 * @return string
	 */
	public function setCookieMode($mode = self::COOKIE_MODE_ONLY) {
		if (self::COOKIE_MODE_NONE === $mode) {
			ini_set('session.use_cookies', '0');
		} else if (self::COOKIE_MODE_ALLOW === $mode) {
			ini_set('session.use_cookies', '1');
			ini_set('session.use_only_cookies', '0');
		} else if (self::COOKIE_MODE_ONLY === $mode) {
			ini_set('session.use_cookies', '1');
			ini_set('session.use_only_cookies', '1');
		} else {
			return false;
		}
		return true;
	}
	
	/**
	 * 获取session进行清理的概率
	 * @return number
	 */
	public function getGCProbability() {
		return (int) ini_get('session.gc_probability');
	}
	
	/**
	 * 设置session进行清理的概率
	 * @param int $probability 概率数
	 * @return string|string
	 */
	public function setGCProbability($probability) {
		if (!is_int($probability) || 0 >= $probability || 100 <= $probability) {
			return false;
		}
		ini_set('session.gc_probability', $probability);
		ini_set('session.gc_divisor', '100');
		return true;
	}
	
	/**
	 * 是否允许sessionid通过url参数传递
	 * @return boolean
	 */
	public function getTransSessionID() {
		return '1' === ini_get('session.use_trans_sid');
	}
	
	/**
	 * 设置是否允许sessionid通过url参数传递
	 * @param int $ifTrans
	 * @return string
	 */
	public function setTransSessionID($ifTrans = 0) {
		return ini_set('session.use_trans_sid', $ifTrans ? '1' : '0');
	}
	
	/**
	 * 获取session存活时间 
	 * @return number
	 */
	public function getSessionLifeTime() {
		return (int) ini_get('session.gc_maxlifetime');
	}
	
	/**
	 * 设置session存活时间
	 * @param int $time
	 * @return number
	 */
	public function setSessionLifeTime($time = 0) {
		return (int) ini_set('session.gc_maxlifetime', (int) $time);
	}
	
	/**
	 * 是否自动启动session
	 * @return boolean
	 */
	public function getAutoStart() {
		return '1' === ini_get('session.auto_start');
	}
	
	/**
	 * 设置自动启动
	 * @param boolean $autostart 是否自动启动
	 * @return string
	 */
	public function setAutoStart($autostart) {
		return ini_set('session.auto_start', $autostart ? '1' : '0');
	}
	
	/**
	 * 获取当前session的文件名
	 * @return string
	 */
	public function getCurrentSessionFileName(){
		return $this->getSavePath().'/sess_'.$this->getSessionId();
	}
	
	public function offsetExists($offset) {
		$this->exist($offset);
	}
	
	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}
	
	public function offsetGet($offset) {
		$this->get($offset);
	}
	public function offsetUnset($offset) {
		$this->remove($offset);
	}
	
	public function getIterator($name = null) {
		return new ArrayObject(($name && isset($_SESSION[$name])) ? $_SESSION[$name] : $_SESSION);
	}
	
	public function count() {
		return count($_SESSION);
	}
}