<?php
/**
 * 会话机制，依赖Cache机制实现，应用可以根据自己的需求配置需要的存储方式实现会话存储
 * 【配置】支持组件配置格式:
 * <pre>
 * 	'WindSession' => array(
 * 		'path'       => 'WIND:http.session.WindSession',
 * 		'scope'      => 'singleton',
 *      'destroy'    => 'close',  //配置在进程结束时使用的方法，执行session  write和close
 * 		'properties' => array(
 * 			'handler' => array(
 * 				'ref' => 'sessionSave',//用户配置的缓存类型--缓存组件的配置格式参照缓存配置文件
 * 			),
 * 		),
 * 	)
 * </pre>
 * 【使用】调用时使用：
 * <pre>
 * $session = $this->getSystemFactory()->getInstance('WindSession');
 * 
 * $session->set('name', 'test');    //等同：$_SESSION['name'] = 'test';
 * echo $session->get('name');       //等同：echo $_SESSION['name'];
 * 
 * $session->delete('name');         //等同： unset($_SESSION['name');
 * echo $session->sessionName();     //等同： echo session_name();
 * echo $session->sessionId();       //等同： echo session_id();
 * $session->destroy();              //等同： session_unset();session_destroy();
 * </pre>
 * 【使用原生】：
 * 如果用户不需要配置自己其他存储方式的session，则不许要修改任何调用，只要在WindSession的配置中将properties配置项去掉即可。如下：
 * <pre>
 * 	'WindSession' => array(
 * 		'path' => 'WIND:http.session.WindSession',
 * 		'scope' => 'singleton',
 * 	)
 * </pre>
 * 【扩展】:
 * 如果用户实现了自己的实现，需要调用自己的实现，则只需要更改path的值指定到自己的实现，即可。
 * 
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$
 * @package
 */
class WindSession extends WindModule {

	protected $handler = null;

	/**
	 * 构造函数
	 * @param AbstractWindCache $handler
	 */
	public function __construct(AbstractWindCache $handler = null) {
		$handler && $this->setHandler($handler);
	}

	/**
	 * 开启session
	 * 
	 * @param string $id
	 */
	public function start() {
		if ('' === $this->sessionId() && '1' !== ini_get('session.auto_start')) {
			session_start();
		}
	}

	/**
	 * 设置数据
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function set($key, $value) {
		is_array($value) || is_object($value) && $value = serialize($value);
		$_SESSION[$key] = $value;
		return true;
	}

	/**
	 * 获得数据
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		$value = isset($_SESSION[$key]) ? $_SESSION[$key] : null;
		$tmp = unserialize($value);
		return (is_array($tmp) || is_object($tmp)) ? $tmp : $value;
	}
	
	/**
	 * 删除数据
	 * 
	 * @param string $key
	 */
	public function delete($key) {
		$_SESSION[$key] = null;
		unset($_SESSION[$key]);
	}
	
	/**
	 * 清除会话信息
	 * 
	 * @return boolean
	 */
	public function destroy() {
		session_unset();
		return session_destroy();
	}
	
	/**
	 * 检测变量是否已经被注册
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function isRegister($key) {
		return session_is_registered($key);
	}
	
	/**
	 * 获得session的名字
	 * 
	 * @return string
	 */
	public function sessionName() {
		return session_name();
	}
	
	/**
	 * 获得sessionId
	 * 
	 * @return string
	 */
	public function sessionId() {
		return session_id();
	}

	/**
	 * 设置链接对象
	 * 
	 * @param AbstractWindCache $handler
	 */
	public function setHandler($handler) {
		if ($handler instanceof AbstractWindCache) {
			$this->handler = $handler;
			$this->close();
			WindSessionHandler::registerHandler();
		}
	}

	/**
	 * 获得链接对象
	 * 
	 * @return AbstractWindCache
	 */
	public function getHandler() {
		return $this->_getHandler();
	}
	
	/**
	 * 在进程结束前执行session的write和close操作
	 */
	public function close() {
		session_write_close();
	}
}

/**
 * 注册session处理的方法
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$
 * @package
 */
final class WindSessionHandler {

	/**
	 * 获得句柄
	 * @return AbstractWindCache
	 */
	static private function getHandler() {
		$session = Wind::getApp()->getComponent('WindSession');
		return $session ? $session->getHandler() : null;
	}

	/**
	 * 打开会话存储机制
	 * 在session_start()执行的时候执行。
	 * 
	 * @param string $savePath
	 * @param string $sessionName
	 * @return  bollean
	 */
	static public function open($savePath, $sessionName) {
		if (null === self::getHandler()) return true;
		$lifeTime = get_cfg_var("session.gc_maxlifetime");
		if (($expire = self::getHandler()->getExpire()) == '0') {
			$lifeTime = get_cfg_var("session.gc_maxlifetime");
			self::getHandler()->setExpire($lifeTime ? $lifeTime : 0);
		} else {
			ini_set("session.gc_maxlifetime", $expire);
		}
		return true;
	}

	/**
	 * 关闭会话存储存储机制
	 * 在页面执行完的时候执行
	 * 
	 * @return  bollean
	 */
	static public function close() {
		return true;
	}

	/**
	 * 将sessionID对应的数据写到存储
	 * 在需要写入session数据的时候执行
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	static public function write($sessID, $sessData) {
		if (null === self::getHandler()) return true;
		return self::getHandler()->set($sessID, $sessData);
	}

	/**
	 * 从存储中装载session数据
	 * 在执行session_start的时候执行在open之后
	 * 
	 * @param mixed $sessid
	 */
	static public function read($sessID) {
		if (null === self::getHandler()) return true;
		return self::getHandler()->get($sessID);
	}

	/**
	 * 对存储系统中的数据进行垃圾收集
	 * 在执行session过期策略的时候执行，注意，session的过期并不是时时的，需要根据php.ini中的配置项：
	 * session.gc_probability = 1
	 * session.gc_divisor = 1000  
	 * 执行的概率是gc_probability/gc_divisor .
	 * session.gc_maxlifetime = 1440  设置的session的过期时间
	 * 
	 * @param mixed $maxlifetime
	 */
	static public function gc($maxlifetime) {
		if (null === self::getHandler()) return true;
		return self::getHandler()->clear(true);
	}

	/**
	 * 破坏与指定的会话ID相关联的数据
	 * 在执行session_destroy的时候执行。
	 * 
	 * @param mixed $name
	 */
	static public function destroy($sessID) {
		if (null === self::getHandler()) return true;
		return self::getHandler()->delete($sessID);
	}
	
	/**
	 * 设置session的存储方法
	 * 同时启动session
	 */
	static public function registerHandler() {
		if (null === self::getHandler()) return true;
		session_set_save_handler(array('WindSessionHandler', 'open'), array('WindSessionHandler', 'close'), array('WindSessionHandler', 'read'), 
			array('WindSessionHandler', 'write'), array('WindSessionHandler', 'destroy'), array('WindSessionHandler', 'gc'));
		Wind::getApp()->getComponent('WindSession')->start();
	}
}
WindSessionHandler::registerHandler();