<?php

/**
 * 用户定义session存储机制
 * the last known user to change this file in the repository  <$LastChangedBy: weihu $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: AbstractWindUserSession.php 1704 2011-03-08 10:40:17Z weihu $ 
 * @package 
 */
abstract class AbstractWindSession extends WindModule {

	protected $handler = null;
	
	/**
	 * 构造函数
	 * @param AbstractWindCache $handler
	 */
	public function __construct(AbstractWindCache $handler = null) {
		$handler && $this->setHandler($handler);
		register_shutdown_function('session_write_close');
	}

	/**
	 * 打开会话存储机制
	 * 在session_start()执行的时候执行。
	 * 
	 * @param string $savePath
	 * @param string $sessionName
	 * @return  bollean
	 */
	public abstract function open($savePath, $sessionName);

	/**
	 * 关闭会话存储存储机制
	 * 在页面执行完的时候执行
	 * 
	 * @return  bollean
	 */
	public abstract function close();

	/**
	 * 将sessionID对应的数据写到存储
	 * 在需要写入session数据的时候执行
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public abstract function write($sessId, $sessData);

	/**
	 * 从存储中装载session数据
	 * 在执行session_start的时候执行在open之后
	 * 
	 * @param mixed $sessid
	 */
	public abstract function read($sessId);

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
	public abstract function gc($maxlifetime);

	/**
	 * 破坏与指定的会话ID相关联的数据
	 * 在执行session_destroy的时候执行。
	 * 
	 * @param mixed $name
	 */
	public abstract function destroy($sessId);

	/**
	 * 开启session
	 * 
	 * @param string $id
	 */
	public function start() {
		$this->getHandler() && $this->setSessionHandler();
		if ('' === session_id() && '1' !== ini_get('session.auto_start')) {
			session_start();
		}
	}

	/**
	 * 设置session的回调函数
	 */
	public function setSessionHandler() {
		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 
			'write'), array($this, 'destroy'), array($this, 'gc'));
	}

	/**
	 * 设置链接对象
	 * 
	 * @param AbstractWindCache $handler
	 */
	public function setHandler($handler) {
		if ($handler instanceof AbstractWindCache) $this->handler = $handler;
	}

	/**
	 * 获得链接对象
	 * 
	 * @return AbstractWindCache
	 */
	public function getHandler() {
		return $this->_getHandler();
	}
}

