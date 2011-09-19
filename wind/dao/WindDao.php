<?php
/**
 * 抽象DAO接口
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDao extends WindModule {
	/**
	 * @var object
	 */
	protected $connection = null;

	/**
	 * 根据用户配置决定配置是采用配置链接管理
	 * @return WindConnection
	 */
	public function getConnection() {
		return $this->_getConnection();
	}

	/**
	 * @param field_type $connection
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}
}
?>