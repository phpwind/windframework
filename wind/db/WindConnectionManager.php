<?php
Wind::import("WIND:db.WindConnection");
/**
 * 配置说明：
 * 1. 当没有任何策略部署时 ，默认返回当前配置中的第一个链接句柄
 * 2. 当没有任何策略部署时，如果在sql语句中有链接句柄指定时则返回指定的链接句柄
 * 例如：'{db1:tableName}'返回db1指定的链接句柄
 * 3. 如果当前有策略部署时，则按照策略部署规则返回
 * 配置格式如下：
 * <connections except='*:db1;user*,tablename2:db1|db2;'>
 * <connection name='db1'>
 * <dsn>mysql:host=localhost;dbname=test</dsn>
 * <user>root</user>
 * <pwd>root</pwd>
 * <charset>utf8</charset>
 * <tablePrefix>pw_</tablePrefix>
 * </connection>
 * <connection name='db2'>
 * <dsn>mysql:host=localhost;dbname=test</dsn>
 * <user>root</user>
 * <pwd>root</pwd>
 * <charset>utf8</charset>
 * <tablePrefix>pw_</tablePrefix>
 * </connection>
 * </connections>
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindConnectionManager extends WindConnection {
	/**
	 * 数据库连接池策略部署配置信息
	 * @var array
	 */
	private $except = array('_current' => '', '_default' => '');
	/**
	 * 链接池
	 * @var array
	 */
	private $pool = array();
	/**
	 * 当前sql语句表名称
	 * @var string
	 */
	private $tableName;
	/**
	 * 当前的sql语句查询类型
	 * @var string
	 */
	private $sqlType;
	private $dbNames = array();

	/* (non-PHPdoc)
	 * @see WindConnection::getDbHandle()
	 */
	public function getDbHandle() {
		$this->init();
		return $this->_dbHandle;
	}

	/* (non-PHPdoc)
	 * @see WindConnection::init()
	 */
	public function init() {
		try {
			if (!isset($this->pool[$this->except['_current']])) {
				$driverName = $this->getDriverName();
				$dbHandleClass = "WIND:db." . $driverName . ".Wind" . ucfirst($driverName) . "PdoAdapter";
				$dbHandleClass = Wind::import($dbHandleClass);
				$_dbHandle = new $dbHandleClass($this->_dsn, $this->_user, $this->_pwd, (array) $this->_attributes);
				$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$_dbHandle->setCharset($this->_charset);
				$this->pool[$this->except['_current']] = $_dbHandle;
			}
			$this->_dbHandle = $this->pool[$this->except['_current']];
			if (WIND_DEBUG & 2)
				Wind::getApp()->getComponent('windLogger')->info(
					"db.WindConnection.init() Initialize db connection success.", 'db');
		} catch (PDOException $e) {
			$this->close();
			throw new WindDbException($e->getMessage());
		}
	}

	/* (non-PHPdoc)
	 * @see WindConnection::parseQueryString()
	 */
	protected function parseQueryString($sql) {
		$sql = preg_replace_callback('/^([a-zA-Z]*)\s[\w\*\s]+(\{([\w]+\:)?([\w]+\.)?([\w]+)\})?[\w\s\<\=\:]*/i', 
			array($this, '_pregQueryString'), $sql);
		if (!$this->except['_current']) {
			//TODO 通配符支持
			$_c = isset($this->except[$this->tableName]) ? $this->except[$this->tableName] : $this->except['_default'];
			$this->_resolveCurrentDb($_c);
			!$this->except['_current'] && $this->except['_current'] = $this->dbNames[0];
		}
		$_config = $this->getConfig($this->except['_current']);
		if (!$_config)
			throw new WindDbException(
				'[db.WindConnectionManager.init] db connection ' . $this->except['_current'] . ' is not exist.');
		parent::_initConfig($_config);
		return parent::parseQueryString($sql);
	}

	/**
	 * @param array $_c
	 */
	private function _resolveCurrentDb($_c) {
		if ($_c)
			switch ($this->sqlType) {
				case 'SELECT':
					if (is_array($_c['_s']) && !empty($_c['_s'])) {
						$_count = count((array) $_c['_s']);
						if ($_count > 1)
							$this->except['_current'] = $_c['_s'][rand(0, $_count - 1)];
						else
							$this->except['_current'] = $_c['_s'][0];
						break;
					}
				default:
					$this->except['_current'] = $_c['_m'];
					break;
			}
	}

	/**
	 * Array(
	 * [0] => SELECT * FROM {db1:database.members} WHERE uid<=:uid	| 匹配到的完整str
	 * [1] => SELECT												| 当前的sql类型
	 * [2] => {db1:database.members}								| 当前table
	 * [3] => db1:													| 当前table自定义链接
	 * [4] => database.												| 当前database
	 * [5] => members												| 当前表名
	 * )
	 * @param array $matchs
	 */
	private function _pregQueryString($matchs) {
		$this->sqlType = $matchs[1];
		if (isset($matchs[2])) {
			$this->tableName = $matchs[5];
			$this->except['_current'] = trim($matchs[3], ':');
			$_return = str_replace($matchs[3] . $matchs[4], '', $matchs[0]);
		} else
			$_return = $matchs[0];
		return $_return;
	}

	/* (non-PHPdoc)
	 * @see WindConnection::_initConfig()
	 */
	protected function _initConfig() {
		if ($_except = $this->getConfig('connections', 'except')) {
			preg_replace_callback('/([\w\*\,]+):([\w]+)\|*([\w\,]+)*/i', array($this, '_pregExcept'), $_except);
			unset($this->_config['connections']['except']);
		}
		$this->_config = $this->getConfig('connections');
		$this->dbNames = array_keys($this->_config);
		if (empty($this->dbNames))
			throw new WindDbException('[db.WindConnectionManager._initConfig] db config is required.');
	}

	/**
	 * 处理链接管理策略
	 * @param array $matchs
	 * @throws WindDbException
	 */
	private function _pregExcept($matchs) {
		$_keys = explode(',', $matchs[1]);
		foreach ($_keys as $_v) {
			if ($_v === '*') {
				$this->except['_default']['_m'] = $matchs[2];
				$this->except['_default']['_s'] = isset($matchs[3]) ? explode(',', $matchs[3]) : array();
				break;
			}
			$this->except[$_v]['_m'] = $matchs[2];
			$this->except[$_v]['_s'] = isset($matchs[3]) ? explode(',', $matchs[3]) : array();
		}
	}
}