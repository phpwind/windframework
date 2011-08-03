<?php
Wind::import("WIND:component.db.WindConnection");
/**
 * 配置格式为：
 * array(
		'class' => 'COM:db.WindConnectionManager',
		'db1' => array(
			'user' => 'xxx',
			'pwd' => 'xxx',
			'dsn' => 'mysql:host=localhost;dbname=test',
			'charset' => 'UTF8',
			'tablePrefix' => 'xx_', //新旧表前缀替换，前一个替换|之后的前缀
		),
		'db2' => array(
			'user' => 'xxx',
			'pwd' => 'xxx',
			'dsn' => 'mysql:host=localhost;dbname=test',
			'charset' => 'UTF8',
			'tablePrefix' => 'xx_', //新旧表前缀替换，前一个替换|之后的前缀
		),
	),
 */
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
	 * 初始化操作，将配置中的class的配置项删除
	 */
	public function init() {
		unset($this->_config['class']);
	}
	
	/**
	 * 根据链接名称返回链接句柄，name为空则返回随机返回一个俩接句柄
	 * @param unknown_type $name
	 * @return WindConnection
	 */
	public function getConnection($name = '') {
		if (isset($this->connections[$name])) return $this->connections[$name];
		$config = array();
		if ($name !== '') {
			$config = $this->getConfig($name);
			if (!is_array($config) || empty($config)) {
				throw new WindDbException('[component.db.WindConnectionManager.getConnection] empty config.');
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
	 * 随机返回当前连接句柄的配置信息
	 * @return array
	 */
	private function getCurrentConnection() {
		$configs = $this->getConfig();
		$keys = array_keys($configs);
		$key = count($keys) > 1 ? $keys[rand(0, count($keys) - 1)] : $keys[0];
		return $configs[$key];
	}
}