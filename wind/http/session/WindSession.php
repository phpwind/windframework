<?php
/**
 * 会话机制，依赖Cache机制实现，应用可以根据自己的需求配置需要的存储方式实现会话存储
 * 【配置】支持组件配置格式:
 * <pre>
 * 'WindSession' => array(
 * 'path'       => 'WIND:http.session.WindSession',
 * 'scope'      => 'singleton',
 * 'destroy'    => 'close',  //配置在进程结束时使用的方法，执行session  write和close
 * 'properties' => array(
 * 'handler' => array(
 * 'ref' => 'sessionSave',//用户配置的缓存类型--缓存组件的配置格式参照缓存配置文件
 * ),
 * ),
 * )
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
 * 'WindSession' => array(
 * 'path' => 'WIND:http.session.WindSession',
 * 'scope' => 'singleton',
 * )
 * </pre>
 * 【切忌】：
 * 虽然框架的组件支持初始化方法的配置initMethod，但是在session这个配置中，当你需要使用其他的存储方式来存储session内容的时候，
 * 是【不允许】被配置的，因为session_set_save_handler必须在session_start之前被设置，而设置了initMethod之后，将会使
 * session_start在session_set_save_handler之前被启动。
 * 
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$
 * @package
 */
class WindSession extends WindModule {

	/**
	 * 构造函数
	 * @param AbstractWindCache $dataStoreHandler
	 */
	public function __construct($dataStoreHandler = null, $sessionHandler = null) {
		$this->setDataStoreHandler($dataStoreHandler, $sessionHandler);
	}

	/**
	 * 开启session
	 * @param string $id
	 */
	public function start() {
		'' === $this->getCurrentId() && session_start();
	}

	/**
	 * 设置数据
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		$key && $_SESSION[$key] = $value;
	}

	/**
	 * 获得数据
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->isRegistered($key) ? $_SESSION[$key] : '';
	}

	/**
	 * 删除数据
	 * @param string $key
	 */
	public function delete($key) {
		return session_unregister($key);
	}

	/**
	 * 清除会话信息
	 * @return boolean
	 */
	public function destroy() {
		return session_destroy();
	}

	/**
	 * 检测变量是否已经被注册
	 * @param string $key
	 * @return boolean
	 */
	public function isRegistered($key) {
		return session_is_registered($key);
	}

	/**
	 * 获得当前session的名字
	 * @return string
	 */
	public function getCurrentName() {
		return session_name();
	}

	/**
	 * 设置当前session的名字
	 * @param string $name
	 */
	public function setCurrentName($name) {
		return session_name($name);
	}

	/**
	 * 获得sessionId
	 * @return string
	 */
	public function getCurrentId() {
		return session_id();
	}

	/**
	 * 设置当前session的Id
	 * @param string $id
	 */
	public function setCurrentId($id) {
		return session_id($id);
	}

	/**
	 * write and close
	 */
	public function commit() {
		return session_commit();
	}

	/**
	 * 设置链接对象
	 * @param AbstractWindCache $handler
	 * @param WindSessionHandler $sessionHandler
	 */
	public function setDataStoreHandler($dataStoreHandler, $sessionHandler = null) {
		if ($dataStoreHandler) {
			if ($sessionHandler === null) {
				Wind::import('WIND:http.session.handler.WindSessionHandler');
				$sessionHandler = new WindSessionHandler();
			}
			$sessionHandler->registerHandler($dataStoreHandler);
		}
		$this->start();
	}
}
