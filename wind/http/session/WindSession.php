<?php
Wind::import('WIND:http.session.AbstractWindSession');
/**
 * 会话机制，依赖Cache机制实现，应用可以根据自己的需求配置需要的存储方式实现会话存储
 * 【配置】支持组件配置格式:
 * <pre>
 * 'WindSession' => array(
 *		'path' => 'WIND:http.session.WindSession',
 *		'scope' => 'singleton',
 *		'properties' => array(
 *			'handler' => array(
 *			    'ref' => 'sessionSave',//用户配置的缓存类型--缓存组件的配置格式参照缓存配置文件
 *			),
 *		),
 *  )
 * </pre>
 * 【使用】调用时使用：
 * <pre>
 * $session = $this->getSystemFactory()->getInstance('WindSession');
 * $session->start();
 * 
 * $_SESSION['name'] = 'test';
 * echo $_SESSION['name'];
 * </pre>
 * 【使用原生】：
 * 如果用户不需要配置自己其他存储方式的session，则不许要修改任何调用，只要在WindSession的配置中将properties配置项去掉即可。如下：
 * <pre>
 * 'WindSession' => array(
 *		'path' => 'WIND:http.session.WindSession',
 *		'scope' => 'singleton',
 *  )
 * </pre>
 * 【扩展】
 * 如果用户实现了自己的实现，需要调用自己的实现，则只需要更改path的值指定到自己的实现，即可。
 * 
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$
 * @package
 */
class WindSession extends AbstractWindSession {
	
	/* (non-PHPdoc)
	 * @see AbstractWindSession::open()
	 */
	public function open($savePath, $sessionName) {
		$handler = $this->getHandler();
		$lifeTime = get_cfg_var("session.gc_maxlifetime");
		if (($expire = $handler->getExpire()) == '0') {
			$lifeTime = get_cfg_var("session.gc_maxlifetime");
			$handler->setExpire($lifeTime ? $lifeTime : 0);
		} else {
			ini_set("session.gc_maxlifetime", $expire);
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSession::close()
	 */
	public function close() {
		session_write_close();
		return true;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSession::write()
	 */
	public function write($sessID, $sessData) {
		return $this->getHandler()->set($sessID, $sessData);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSession::read()
	 */
	public function read($sessID) {
		return $this->getHandler()->get($sessID);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSession::gc()
	 */
	public function gc($maxlifetime) {
		return $this->getHandler()->clear(true);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindSession::destroy()
	 */
	public function destroy($sessID) {
		return $this->getHandler()->delete($sessID);
	}
}

