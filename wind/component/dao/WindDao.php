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
	 * 链接配置文件或是配置数组
	 * @var string|array
	 */
	protected $dbName = '';
	/**
	 * cache类型定义
	 *
	 * @var string
	 */
	protected $cacheClass = '';
	/**
	 * cache配置信息
	 *
	 * @var string
	 */
	protected $cacheConfig = '';
	
	/**
	 * @var object
	 */
	protected $connection = null;
	private $cacheHandler = null;

	/**
	 * 获得需要缓存的处理的方法名称数组
	 * array('methodName1'=>'WIND:component')
	 * @return multitype:
	 */
	public function getCacheMethods() {
		return array();
	}

	/**
	 * @return WindConnection
	 */
	public function getConnection() {
		return $this->_getConnection();
	}

	/**
	 * @return WindCacheDb
	 */
	public function getCacheHandler() {
		return $this->_getCacheHandler();
	}

	/**
	 * @return the $configName
	 */
	public function getDBName() {
		return $this->dbName;
	}

	/**
	 * @return the $cacheClass
	 */
	public function getCacheClass() {
		return $this->cacheClass;
	}

	/**
	 * @return the $cacheConfig
	 */
	public function getCacheConfig() {
		return $this->cacheConfig;
	}

	/**
	 * @param field_type $connection
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}

	/**
	 * @param field_type $cacheHandler
	 */
	public function setCacheHandler($cacheHandler) {
		$this->cacheHandler = $cacheHandler;
	}

}
?>