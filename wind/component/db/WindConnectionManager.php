<?php
Wind::import("WIND:component.db.WindConnection");
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindConnectionManager extends WindModule {
	const CONNECTION_MODE_RAND = '0';
	/**
	 * 链接句柄
	 *
	 * @var array
	 */
	private $connections = array();

	/**
	 * 根据链接名称返回链接句柄，name为空则返回随机返回一个俩接句柄
	 * @param unknown_type $name
	 * @return WindConnection
	 */
	public function getConnection($name = '') {
		if (isset($this->connections[$name])) return $this->connections[$name];
		$configs = $this->getConfig();
		if (!is_array($configs) || empty($configs)) {
			throw new WindDbException('[component.db.WindConnectionManager.getConnection] empty config.');
		}
		$config = array();
		if ($name !== '') {
			foreach ($configs as $value) {
				if ($value['name'] == $name) {
					$config = $value;
					break;
				}
			}
		} else {
			$config = $this->getCurrentConnection();
			$name = $config['name'];
		}
		$connection = new WindConnection();
		$connection->setConfig($config);
		$connection->init();
		$this->connections[$name] = $connection;
		return $this->connections[$name];
	}

	/**
	 * 返回当前连接句柄的配置信息
	 */
	protected function getCurrentConnection() {
		$configs = $this->getConfig();
		return count($configs) > 1 ? $configs[rand(0, count($configs) - 1)] : $configs[0];
	}
}