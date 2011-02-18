<?php

L::import('WIND:component.db.WindConnectionManager');

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
	 * @var WindDbAdapter 数据库操作适配器
	 */
	protected $connection = null;

	public function __construct(array $config) {
		$this->distributed = new WindConnectionManager($config);
		$this->getMasterConnection();
	}

	/**
	 * 将查询的结果作为对象返回
	 * @param string $sql 要执行的sql语句
	 * @param string $fetch_type 从结果集中取得一行作为关联数组，或数字数组，或二者兼有
	 * @param boolean $colAsProp 是否将结查询的结果的字段名作为对象的属性.
	 * @return stdClass
	 */
	public function queryForObject($sql, $fetch_type = IWindDbConfig::RESULT_ASSOC, $colAsProp = true) {
		$result = $this->queryBySql($sql, $fetch_type);
		return $this->bindValueToObject(new stdClass(), $result, $colAsProp);
	}

	/**
	 * 将查询的结果作为数组返回
	 * @param string $sql 要执行的sql语句
	 * @param string $fetch_type 从结果集中取得一行作为关联数组，或数字数组，或二者兼有
	 * @return array
	 */
	public function queryForArray($sql, $fetch_type = IWindDbConfig::RESULT_ASSOC) {
		return $this->queryBySql($sql, $fetch_type);
	}

	/**
	 * 新增操作
	 * @param string $sql
	 * @return boolean;
	 */
	public function insert($sql) {
		return $this->write($sql);
	}

	/**
	 * 删除操作
	 * @param string $sql
	 * @return boolean;
	 */
	public function delete($sql) {
		return $this->write($sql);
	}

	/**
	 * 更新操作
	 * @param string $sql
	 * @return boolean;
	 */
	public function update($sql) {
		return $this->write($sql);
	}

	/**
	 * 查询操作
	 * @param string $sql
	 * @return array;
	 */
	public function queryBySql($sql, $fetch_type = IWindDbConfig::RESULT_ASSOC) {
		if (true === ($query = $this->read($sql))) {
			return $this->getConnection()->getAllRow($fetch_type);
		}
		return array();
	}

	/**
	 * 将查询的结果作为对象返回，采用sql语句组装器实现要执行的sql语句,，便于实现跨数据库引挚操作
	 * @param WindSqlBuilder $builder sql语句组装器
	 * @param string $fetch_type 从结果集中取得一行作为关联数组，或数字数组，或二者兼有
	 * @param boolean $colAsProp 是否将结查询的结果的字段名作为对象的属性.
	 * @return stdClass
	 */
	public function queryForObjectByBuilder(WindSqlBuilder $builder, $fetch_type = IWindDbConfig::RESULT_ASSOC, $colAsProp = true) {
		$result = $this->queryByBuilder($builder, $fetch_type);
		return $this->bindValueToObject(new stdClass(), $result, $colAsProp);
	}

	/**
	 * 将查询的结果作为数组返回,采用sql语句组装器实现要执行的sql语句,，便于实现跨数据库引挚操作
	 * @param WindSqlBuilder $builder sql语句组装器
	 * @param string $fetch_type 从结果集中取得一行作为关联数组，或数字数组，或二者兼有
	 * @return array
	 */
	public function queryForArrayByBuilder(WindSqlBuilder $builder, $fetch_type = IWindDbConfig::RESULT_ASSOC) {
		return $this->queryByBuilder($builder, $fetch_type);
	}

	/**
	 * 新增操作,由sql语句组装器组装的sql语句操作，便于实现跨数据库引挚操作
	 * 哎，如果php支持多态那是多好啊
	 * @param WindSqlBuilder $builder sql语句生成器
	 * @return boolean;
	 */
	public function insertByBuilder(WindSqlBuilder $builder) {
		return $this->write($builder->getInsertSql());
	}

	/**
	 * 删除操作,由sql语句组装器组装的sql语句操作，便于实现跨数据库引挚操作
	 * @param WindSqlBuilder $builder
	 * @return boolean;
	 */
	public function deleteByBuilder(WindSqlBuilder $builder) {
		return $this->write($builder->getDeleteSql());
	}

	/**
	 * 更新操作,由sql语句组装器组装的sql语句操作，便于实现跨数据库引挚操作
	 * @param WindSqlBuilder $builder
	 * @return boolean;
	 */
	public function updateByBuilder(WindSqlBuilder $builder) {
		return $this->write($builder->getUpdateSql());
	}

	/**
	 * 查询操作,由sql语句组装器组装的sql语句操作，便于实现跨数据库引挚操作
	 * @param WindSqlBuilder $builder
	 * @param string $fetch_type
	 * @return array
	 */
	public function queryByBuilder(WindSqlBuilder $builder, $fetch_type = IWindDbConfig::RESULT_ASSOC) {
		if (true === ($query = $this->read($builder->getSelectSql()))) {
			return $this->getConnection()->getAllRow($fetch_type);
		}
		return array();
	}

	/**
	 * 分布式采取读写分离，从主服务器执行写的操作
	 * @param string $sql
	 * @return boolean;
	 */
	public function write($sql) {
		return $this->getMasterConnection()->query($sql);
	}

	/**
	 * 分布式采取读写分离，从从服务器执行读的操作
	 * @param string $sql
	 * @return boolean;
	 */
	public function read($sql) {
		return $this->getSlaveConnection()->query($sql);
	}

	/**
	 * 获取分布式数据库适配器管理工厂
	 * @return WindConnectionManager
	 */
	public function getDistributed() {
		return $this->distributed;
	}

	/**
	 * 获取当前数据库操作的适配器
	 * @return WindDbAdapter
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * 获取主数据库适配器
	 * @return WindDbAdapter
	 */
	public function getMasterConnection() {
		return $this->connection = $this->getDistributed()->getMasterConnection();
	}

	/**
	 * 获取从数据库适配器
	 * @return WindDbAdapter
	 */
	public function getSlaveConnection() {
		return $this->connection = $this->getDistributed()->getSlaveConnection();
	}

	/**
	 * 获取当前数据库操作的适配器对应的sql语句组装器
	 * @return WindSqlBuilder
	 */
	public function getSqlBuilder() {
		return $this->getConnection()->getSqlBuilder();
	}

	/**
	 * 将数组格式的访问转化为数组
	 * @param stdClass $object 对象的初始化
	 * @param array $value     要绑定到指定对象的值
	 * @param boolean $colAsProp 是否将数组的列也绑到子对象中
	 * @return stdClass
	 */
	public function bindValueToObject(stdClass $object, array $value, $colAsProp = true) {
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