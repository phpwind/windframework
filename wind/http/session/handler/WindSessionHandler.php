<?php

/**
 * 注册session处理的方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$
 * @package
 */
class WindSessionHandler extends AbstractWindSessionHandler {

	/* (non-PHPdoc)
	 * @see AbstractWindSessionHandler::open($savePath, $sessionName)
	 */
	public function open($savePath, $sessionName) {
		if ('0' == ($expire = $this->dataStore->getExpire())) {
			$lifeTime = get_cfg_var("session.gc_maxlifetime");
			$this->dataStore->setExpire((int) $lifeTime);
		} else
			session_cache_expire($expire);
		return true;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSessionHandler::close()
	 */
	public function close() {
		return true;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSessionHandler::write($sessID, $sessData)
	 */
	public function write($sessID, $sessData) {
		return $this->dataStore->set($sessID, $sessData);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSessionHandler::read($sessID)
	 */
	public function read($sessID) {
		return $this->dataStore->get($sessID);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSessionHandler::gc($maxlifetime)
	 */
	public function gc($maxlifetime) {
		return $this->dataStore->clear();
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSessionHandler::destroy($sessID)
	 */
	public function destroy($sessID) {
		return $this->dataStore->delete($sessID);
	}
}

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindSessionHandler {
	/**
	 * @var AbstractWindCache
	 */
	protected $dataStore = null;

	/**
	 * 打开会话存储机制
	 * 
	 * @param string $savePath
	 * @param string $sessionName
	 * @return  boolean
	 */
	abstract public function open($savePath, $sessionName);

	/**
	 * 关闭会话存储存储机制
	 * 在页面执行完的时候执行
	 * 
	 * @return  bollean
	 */
	abstract public function close();

	/**
	 * 将sessionID对应的数据写到存储
	 * 在需要写入session数据的时候执行
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	abstract public function write($sessID, $sessData);

	/**
	 * 从存储中装载session数据
	 * 在执行session_start的时候执行在open之后
	 * 
	 * @param mixed $sessid
	 */
	abstract public function read($sessID);

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
	abstract public function gc($maxlifetime);

	/**
	 * 破坏与指定的会话ID相关联的数据
	 * 在执行session_destroy的时候执行。
	 * 
	 * @param mixed $name
	 */
	abstract public function destroy($sessID);

	/**
	 * 设置session的存储方法
	 * @param AbstractWindCache $dataStore
	 */
	public function registerHandler($dataStore) {
		if (!$dataStore instanceof AbstractWindCache) {
			throw new WindException(
				'[http.session.WindSessionHandler.registerHandler] register session save handler fail.', 
				WindException::ERROR_PARAMETER_TYPE_ERROR);
		}
		$this->dataStore = $dataStore;
		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), 
			array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
	}
}