<?php
/**
 * @author Su Qian <weihu@alibaba-inc.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.AbstractWindCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
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

	public function __construct(AbstractWindDbAdapter $dbHandler = null) {
		$dbHandler && $this->setDbHandler($dbHandler);
	}

	/* 
	 * @see AbstractWindCache#set()
	 */
	public function set($key, $value, $expire = 0, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire ? $this->getExpire() : $expire;
		$data = $this->dbHandler->getSqlBuilder()->from($this->table)->field($this->expireField)->where($this->keyField . ' = :key ', array(
			':key' => $this->buildSecurityKey($key)))->select()->getRow();
		if ($data) {
			return !$this->expirestrage && '0' === $data[$this->expireField] ? null : $this->update($key, $value, $expire, $denpendency);
		} else {
			return $this->store($key, $value, $expire, $denpendency);
		}
		return true;
	}

	/* 
	 * @see AbstractWindCache#get()
	 */
	public function get($key) {
		if ($this->expirestrage) {
			$data = $this->dbHandler->getSqlBuilder()->from($this->table)->field($this->valueField)->where($this->keyField . ' = :key ', array(
				':key' => $this->buildSecurityKey($key)))->select()->getRow();
		} else {
			$data = $this->dbHandler->getSqlBuilder()->from($this->table)->field($this->valueField)->where($this->expireField . ' != 0 AND ' . $this->keyField . ' = :key ', array(
				':key' => $this->buildSecurityKey($key)))->select()->getRow();
		}
		return $this->getDataFromMeta($key, unserialize($data[$this->valueField]));
	}

	/* 
	 * @see AbstractWindCache#batchFetch()
	 */
	public function batchGet(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		if (true === $this->expirestrage) {
			$data = $this->dbHandler->getSqlBuilder()->from($this->table)->field($this->valueField, $this->keyField)->where($this->keyField . ' in ( :key ) ', array(
				':key' => $keys))->select()->getAllRow();
		} else {
			$data = $this->dbHandler->getSqlBuilder()->from($this->table)->field($this->valueField, $this->keyField)->where($this->expireField . ' != 0 AND ' . $this->keyField . ' in ( :key ) ', array(
				':key' => $keys))->select()->getAllRow();
		}
		$result = $changed = array();
		foreach ($data as $_data) {
			$_data = unserialize($_data[$this->valueField]);
			if (isset($_data[self::DEPENDENCY]) && $_data[self::DEPENDENCY] instanceof IWindCacheDependency) {
				if ($_data[self::DEPENDENCY]->hasChanged()) {
					$changed[] = $_data[$this->keyField];
				} else {
					$result[$_data[$this->keyField]] = $_data[self::DATA];
				}
			}
		}
		$changed && $this->batchDelete($changed);
		return $result;
	}

	/* 
	 * @see AbstractWindCache#delete()
	 */
	public function delete($key) {
		if ($this->expirestrage) {
			$this->dbHandler->getSqlBuilder()->from($this->table)->where($this->keyField . ' = :key ', array(
				':key' => $this->buildSecurityKey($key)))->delete();
		} else {
			$this->dbHandler->getSqlBuilder()->from($this->table)->set($this->expireField . ' = 0')->where($this->keyField . ' = :key ', array(
				':key' => $this->buildSecurityKey($key)))->update();
		}
	}

	/* 
	 * @see AbstractWindCache#batchDelete()
	 */
	public function batchDelete(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		if ($this->expirestrage) {
			$this->dbHandler->getSqlBuilder()->from($this->table)->where($this->keyField . ' in (:key) ', array(
				':key' => $keys))->delete();
		} else {
			$this->dbHandler->getSqlBuilder()->from($this->table)->set($this->expireField . ' = 0')->where($this->keyField . ' in (:key) ', array(
				':key' => $keys))->update();
		}
	}

	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		if ($this->expirestrage) {
			return $this->dbHandler->getSqlBuilder()->from($this->table)->delete();
		} else {
			return $this->dbHandler->getSqlBuilder()->from($this->table)->set($this->expireField . ' = 0')->update();
		}
		return false;
	}

	/**
	 * 删除过期数据
	 */
	public function deleteExpiredCache() {
		if ($this->expirestrage) {
			$this->dbHandler->getSqlBuilder()->from($this->table)->where($this->expireField . ' !=0 AND ' . $this->expireField . ' < :expires', array(
				':expires' => time()))->delete();
		} else {
			$this->dbHandler->getSqlBuilder()->from($this->table)->set($this->expireField . ' = 0')->where($this->expireField . ' < :expires', array(
				':expires' => time()))->update();
		}
	}

	public function setDbHandler(AbstractWindDbAdapter $dbHandler) {
		$this->dbHandler = $dbHandler;
	}

	/* 
	 * @see WindComponentModule#setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->expirestrage = 'true' === $this->getTableConfig(self::STRAGE,WIND_CONFIG_VALUE);
		$this->table = $this->getTableConfig(self::TABLENAME,WIND_CONFIG_VALUE);
		$this->keyField = $this->getTableConfig(self::KEY,WIND_CONFIG_VALUE);
		$this->valueField = $this->getTableConfig(self::VALUE,WIND_CONFIG_VALUE);
		$this->expireField = $this->getTableConfig(self::EXPIRE,WIND_CONFIG_VALUE);;
	}

	/**
	 * @return mixed
	 */
	protected function getTableConfig($name = '', $subname = '') {
		$tableConfig = $this->getConfig()->getConfig(self::CACHETABLE);
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
	protected function store($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$data = addslashes($this->storeData($value, $expires, $denpendency));
		if ($expires) {
			$expires += time();
		}
		return $this->dbHandler->getSqlBuilder()->from($this->table)->field($this->keyField, $this->valueField, $this->expireField)->data($this->buildSecurityKey($key), $data, $expires)->insert();
	}

	/**
	 * 更新数据
	 * @param string $key
	 * @param int $value
	 * @param int $expires
	 * @param IWindCacheDependency $denpendency
	 * @return boolean
	 */
	protected function update($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$data = $this->storeData($value, $expires, $denpendency);
		if ($expires) {
			$expires += time();
		}
		return $this->dbHandler->getSqlBuilder()->from($this->table)->set($this->valueField . ' = :value,' . $this->expireField . ' = :expires', array(
			':value' => $data, ':expires' => $expires))->where($this->keyField . '=:key', array(
			':key' => $this->buildSecurityKey($key)))->update();
	}

	public function __destruct() {
		if (null !== $this->dbHandler) {
			$this->deleteExpiredCache();
		}
	}

}