<?php
/**
 * @author xiaoxiao <xiaoxia.xuxx@aliyun.com>  2011-7-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.cache.AbstractWindCache');
class WindDbCache extends AbstractWindCache {

	/**
	 * 分布式管理
	 * @var AbstractWindDbAdapter 
	 */
	protected $dbHandler;

	/**
	 * 缓存表
	 * @var string 
	 */
	protected $table = 'pw_cache';

	/**
	 * 缓存表的键字段
	 * @var string 
	 */
	protected $keyField = 'key';

	/**
	 * 缓存表的值字段
	 * @var string 
	 */
	protected $valueField = 'value';

	/**
	 * 缓存表过期时间字段
	 * @var string 
	 */
	protected $expireField = 'expire';

	/**
	 * 数据过期策略
	 * @var boolean 
	 */
	protected $expirestrage = true;

	const CACHETABLE = 'cache-table';

	const TABLENAME = 'table-name';

	const KEY = 'field-key';

	const VALUE = 'field-value';

	const EXPIRE = 'field-expire';

	const STRAGE = 'expirestrage';

	public function __construct(WindConnection $connection = null, $config = array()) {
		$connection && $this->setConnection($connection);
		$config && $this->setConfig($config);
	}

	/* 
	 * @see AbstractWindCache#addValue()
	 */
	protected function addValue($key, $value, $expire = 0) {
		(mt_rand(100, 1000000) % 100 == 0) && $this->deleteExpiredCache();
		$sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE `' . $this->keyField . '` =?' ; 
		$data = $this->getConnection()->createStatement($sql)->getOne(array($key));
		if ($data) {
			return $this->update($key, $value, $expire);
		}
		return $this->store($key, $value, $expire);
	}

	/* 
	 * @see AbstractWindCache#getValue()
	 */
	protected function getValue($key) {
		$sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE `' . $this->keyField . '` =? AND (`' . $this->expireField . '`=0 OR `' . $this->expireField . '`>?)';
		$data = $this->getConnection()->createStatement($sql)->getOne(array($key), time())); 
		return $data[$this->valueField];
	}

	/* 
	 * @see AbstractWindCache#batchFetch()
	 */
	public function batchGet(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		list($sql, $result) = array('', array());
		$sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE `' . $this->keyField . '` IN ' . $this->getConnection()->quoteArray($keys) . ' AND (`' . $this->expireField . '`=0 OR `' . $this->expireField . '`>?)';
		$data = $this->getConnection()->createStatement($sql)->queryAll(array(time()));
		foreach ($data as $tmp) {
			$result[] = $this->formatData($tmp[$this->valueField]);
		}
		return $result;
	}

	/* 
	 * @see AbstractWindCache#deleteValue()
	 */
	protected function deleteValue($key) {
		$sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE `' . $this->keyField . '` = ? ';
		return $this->getConnection()->createStatement($sql)->update(array($key));
	}

	/* 
	 * @see AbstractWindCache#batchDelete()
	 */
	public function batchDelete(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		$sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE `' . $this->keyField . '` IN ' . $this->getConnection()->quoteArray($keys);
		return $this->getConnection()->execute($sql);
	}

	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		return $this->getConnection()->execute('DELETE FROM ' . $this->getTableName());
	}

	/**
	 * 删除过期数据
	 */
	public function deleteExpiredCache() {
		$sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE `' . $this->expireField . '`>0 AND `' . $this->expireField . '`<'.time();
		return $this->getConnection()->execute($sql);
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->dbConfig = $this->getConfig(self::DBCONFIG);
		
		$this->table = $this->getTableConfig(self::TABLENAME);
		$this->keyField = $this->getTableConfig(self::KEY);
		$this->valueField = $this->getTableConfig(self::VALUE);
		$this->expireField = $this->getTableConfig(self::EXPIRE);
	}

	/**
	 * @return mixed
	 */
	private function getTableConfig($name = '', $subname = '') {
		$tableConfig = $this->getConfig(self::CACHETABLE);
		if (empty($name)) {
			return $tableConfig;
		}
		if (empty($subname)) {
			return isset($tableConfig[$name]) ? $tableConfig[$name] : $tableConfig;
		}
		return isset($tableConfig[$name][$subname]) ? $tableConfig[$name][$subname] : $tableConfig[$name];
	}

	/**
	 * 存储数据
	 * @param string $key
	 * @param string $value
	 * @param int $expires
	 * @param IWindCacheDependency $denpendency
	 * @return boolean
	 */
	protected function store($key, $value, $expires = 0) {
		($expires > 0) ? $expires += time() : $expire=0;
		$db = array($this->keyField => $key, $this->valueField => $value, $this->expireField => $expires);
	    $sql = 'INSERT INTO ' . $this->getTableName() . ' SET ' . $this->getConnection()->sqlSingle($db);
		return $this->getConnection()->createStatement($sql)->update();
	}

	/**
	 * 更新数据
	 * @param string $key
	 * @param int $value
	 * @param int $expires
	 * @param IWindCacheDependency $denpendency
	 * @return boolean
	 */
	protected function update($key, $value, $expires = 0) {
		($expires > 0) ? $expires += time() : $expire=0;
		$db = array($this->valueField => $value, $this->expireField => $expires);
		$sql = "UPDATE " . $this->getTableName() . ' SET ' . $this->getConnection()->sqlSingle($db) . ' WHERE `' . $this->keyField . '`=?';
		return $this->getConnection()->createStatement($sql)->update(array($key), true);
	}

	public function __destruct() {
		if (null !== $this->dbHandler) {
			$this->deleteExpiredCache();
		}
	}
	
	/**
	 * 返回表名
	 * @return string 
	 */
	private function getTableName() {
		return $this->table;
	}
	
	/**
	 * 获得链接对象
	 * @return WindConnection 
	 */
	private function getConnection() {
		if (null == $this->connection) {
			$this->connection = new WindConnection();
			$this->connection->setConfig($this->dbConfig);
			$this->connection->init();
		}
		return $this->connection;
	}
	
	/**
	 * @return mixed
	 */
	private function getTableConfig($name = '', $subname = '') {
		$tableConfig = $this->getConfig(self::CACHETABLE);
		if (empty($name)) {
			return $tableConfig;
		}
		if (empty($subname)) {
			return isset($tableConfig[$name]) ? $tableConfig[$name] : $tableConfig;
		}
		return isset($tableConfig[$name][$subname]) ? $tableConfig[$name][$subname] : $tableConfig[$name];
	}
}