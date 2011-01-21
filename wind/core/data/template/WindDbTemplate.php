<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindDbTemplate extends WindTemplate {
	/**
	 * @var WindConnectionManager 分布式管理与数据库驱动工厂
	 */
	protected $distributed = null;
	/**
	 * @var WindSqlBuilder sql语句组装器
	 */
	protected $sqlBuilder = null;
	/**
	 * @var WindDbAdapter 数据库操作适配器
	 */
	protected $connection = null;
	
	/**
	 * @var boolean 是否是分布式
	 */
	protected $ifMutiDb = false;
	
	public function __construct(array $config) {
		$className = L::import('WIND:component.db.WindConnectionManager');
		$this->distributed = new WindConnectionManager($config);
		$this->ifMutiDb = 1 < count($config[IWindDbConfig::CONNECTIONS]);
	
	}
	
	public function setConnAndBuilder($type = IWindDbConfig::CONN_MASTER) {
		if ($this->ifMutiDb) {
			$this->connection = $this->distributed->getConnection('', $type);
		} else {
			$this->connection = $this->distributed->getConnection('', '');
		}
		$this->sqlBuilder = $this->connection->getSqlBuilder();
	}
	
	public function queryForObject($sql, $fetch_type = IWindDbConfig::RESULT_ASSOC, $colAsProp = true) {
		$result = $this->query($sql, $fetch_type);
		return $this->bindValueToObject(new stdClass(), $result, $colAsProp);
	}
	
	public function queryForArray($sql, $fetch_type = IWindDbConfig::RESULT_ASSOC) {
		return $this->query($sql, $fetch_type);
	}
	
	public function insert($sql) {
		return $this->write($sql);
	}
	
	public function delete($sql) {
		return $this->write($sql);
	}
	
	public function update($sql) {
		return $this->write($sql);
	}
	
	public function query($sql, $fetch_type = IWindDbConfig::RESULT_ASSOC) {
		if (true === ($query = $this->read($sql))) {
			return $this->connection->getAllRow($fetch_type);
		}
		return array();
	}
	
	public function write($sql) {
		$this->setConnAndBuilder(IWindDbConfig::CONN_MASTER);
		return $this->connection->query($sql);
	}
	
	public function read($sql) {
		$this->setConnAndBuilder(IWindDbConfig::CONN_SLAVE);
		return $this->connection->query($sql);
	}
	
	/**
	 * 将数组格工的访问转化为数组
	 * @param stdClass $object 对象的初始化
	 * @param array $value     要绑定到指定对象的值
	 * @param boolean $colAsProp 是否将数组的列也绑到子对象中
	 * @return stdClass
	 */
	private function bindValueToObject(stdClass $object, array $value, $colAsProp = true) {
		foreach ($value as $key => $_value) {
			$_hasProp = $colAsProp && is_array($_value);
			$_key = is_string($key) ? $key : '_' . $key;
			$tmp = $_hasProp ? new stdClass() : $_value;
			$object->$_key = $tmp;
			if ($_hasProp) {
				$this->bindValueToObject($object->$_key, $_value, $colAsProp);
			}
		}
		return $object;
	}

}