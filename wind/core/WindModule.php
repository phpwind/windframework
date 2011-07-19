<?php
Wind::import('COM:utility.WindUtility');
/**
 * 所有module的基础抽象类
 * 主要实现__get(), __set()等方法
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 */
class WindModule {
	/**
	 * @var WindConfig
	 */
	private $_config = null;
	/**
	 * 该对象的数组形态
	 * 
	 * @var array
	 */
	private $_array = array();
	/**
	 * 是否进行类型验证
	 *
	 * @var boolean
	 */
	protected $_typeValidation = false;
	/**
	 * 请求参数信息
	 * 
	 * @var array
	 */
	private $delayAttributes = array();

	/**
	 * 设置属性值，该属性的访问类型不能为‘private’类型
	 * 
	 * @param string $propertyName
	 * @param string $value
	 * @return
	 */
	public function __set($propertyName, $value) {
		$_setter = 'set' . ucfirst($propertyName);
		if (method_exists($this, $_setter))
			$this->$_setter($value);
		else
			Wind::log('[core.WindModule.__set] both of property and setter are not exist. ' . $propertyName, 
				WindLogger::LEVEL_DEBUG, 'wind.core');
	}

	/**
	 * 返回输入的属性的值，如果该属性不存在或访问类型为‘private’类型，则返回null
	 * 
	 * @param string $propertyName
	 * @return value of the property or null
	 */
	public function __get($propertyName) {
		$_getter = 'get' . ucfirst($propertyName);
		if (method_exists($this, $_getter))
			return $this->$_getter();
		else
			Wind::log('[core.WindModule.__set] both of property and getter are not exist. ' . $propertyName, 
				WindLogger::LEVEL_DEBUG, 'wind.core');
	}

	/**
	 * 实现setter或者getter方法调用,并返回你调用方法的返回值
	 * 
	 * @param string $methodName
	 * @param array $args
	 * @return the return of the method your call
	 */
	public function __call($methodName, $args) {
		$_prefix = substr($methodName, 0, 4);
		$_propertyName = substr($methodName, 4);
		$_propertyName = WindUtility::lcfirst($_propertyName);
		if ($_prefix == '_set') {
			$this->$_propertyName = $args[0];
		} elseif ($_prefix == '_get') {
			if (!$this->$_propertyName && isset($this->delayAttributes[$_propertyName])) {
				$_instance = null;
				$_property = $this->delayAttributes[$_propertyName];
				if (isset($_property[WindClassDefinition::REF])) {
					$_ref = $_property[WindClassDefinition::REF];
					if ($this->getSystemFactory()->checkAlias($_ref))
						$_instance = $this->getSystemFactory()->getInstance($_ref);
					else
						$_instance = $this->getSystemFactory()->createInstance($_ref);
				}
				$this->$_propertyName = $_instance;
			}
			return $this->$_propertyName;
		}
		throw new WindException('[core.WindModule.__call] ' . get_class($this) . '->' . $methodName . '()', 
			WindException::ERROR_CLASS_METHOD_NOT_EXIST);
	}

	/**
	 * 对象clone魔术方法
	 */
	public function __clone() {
		foreach ($this->writeTableCloneProperty() as $value) {
			if (!is_object($this->$value) || !isset($this->$value)) {
				Wind::log(
					"[core.WindModule.__clone] unexcepted value type or the property 
					is not setted.(" . $value . ") need an object type in here.
					", WindLogger::LEVEL_DEBUG, 'wind.core');
				continue;
			}
			$this->$value = clone $this->$value;
		}
	}

	/**
	 * 返回该对象的数组类型
	 * 
	 * @return array
	 */
	public function toArray() {
		if (empty($this->_array)) {
			$reflection = new ReflectionClass(get_class($this));
			$properties = $reflection->getProperties();
			$_result = array();
			foreach ($properties as $property) {
				$_propertyName = $property->name;
				$_result[$_propertyName] = $this->$_propertyName;
			}
			$this->_array = $_result;
		}
		return $this->_array;
	}

	/**
	 * 验证白名单是否为空，或属性值是否存在于定义的白名单中
	 * @deprecated
	 * @param string $propertyName
	 * @return boolean
	 */
	protected function validatePropertyName($propertyName, $value = null) {
		if (isset($this->delayAttributes[$propertyName])) {
			Wind::log('[core.WindModule.validatePropertyName] is a delay property  (' . $propertyName . ')', 
				WindLogger::LEVEL_DEBUG, 'wind.core');
			return true;
		}
		if (!($_writeTableProperties = $this->writeTableForProperty())) {
			Wind::log(
				"[core.WindModule.validatePropertyName] 
				writeTableForProperty is empty or your input is not exists.
				(" . $propertyName . ")", WindLogger::LEVEL_DEBUG, 'wind.core');
			return false;
		}
		if (!array_key_exists($propertyName, $_writeTableProperties)) {
			Wind::log(
				"[core.WindModule.validatePropertyName] 
				writeTableForProperty is empty or your input is not exists.
				(" . $propertyName . ")", WindLogger::LEVEL_DEBUG, 'wind.core');
			return false;
		}
		if ($this->_typeValidation && $_writeTableProperties[$propertyName]) {
			if ($value instanceof $_writeTableProperties[$propertyName]) return true;
			Wind::log(
				"[core.WindModule.validatePropertyName]
				type of the property " . $propertyName . " is not defined.
				", WindLogger::LEVEL_DEBUG, 'wind.core');
			return false;
		}
		return true;
	}

	/**
	 * 根据配置名取得相应的配置
	 * 
	 * @param string $configName 键名
	 * @param string $subConfigName 二级键名
	 * @param array $default 默认值
	 * @return string|array
	 */
	public function getConfig($configName = '', $subConfigName = '', $default = '') {
		if ($this->_config === null) {
			Wind::log('[core.WindModule.getConfig] config is not exist.', WindLogger::LEVEL_INFO, 'wind.core');
			return $default;
		}
		return $this->_config->getConfig($configName, $subConfigName, array(), $default);
	}

	/**
	 * Config配置,如果配置信息已经存在，则会合并配置
	 * 
	 * @param string|array|windConfig $config
	 */
	public function setConfig($config) {
		if (!$config) return;
		if (is_string($config)) {
			Wind::import('WIND:core.config.parser.WindConfigParser');
			$config = new WindConfig($config, new WindConfigParser(), get_class($this), WIND_CONFIG_CACHE);
		} elseif (is_array($config))
			$config = new WindConfig($config);
		
		if ($this->_config !== null)
			$this->_config->setConfig($config->getConfig(), true);
		else
			$this->_config = $config;
	}

	/**
	 * 设置自动实现Getter/Setter方法的属性名称
	 * 当该方法返回值为空时，类属性的可访问性跟默认相同
	 * @deprecated
	 * @return array
	 */
	protected function writeTableForProperty() {
		return array('delayAttributes' => 'array');
	}

	/**
	 * 通过重载该方法，可以实现对对象内部的对象同时进行clone
	 * 返回需要被clone的对象数组
	 * 
	 * @return array
	 */
	protected function writeTableCloneProperty() {
		return array();
	}

	/**
	 * @return WindSystemConfig
	 */
	protected function getSystemConfig() {
		return Wind::getApp()->getWindSystemConfig();
	}

	/**
	 * @return WindFactory
	 */
	protected function getSystemFactory() {
		return Wind::getApp()->getWindFactory();
	}

	/**
	 * @return WindHttpRequest
	 */
	protected function getRequest() {
		return Wind::getApp()->getRequest();
	}

	/**
	 * @return WindHttpResponse
	 */
	protected function getResponse() {
		return Wind::getApp()->getResponse();
	}

	/**
	 * @param array $delayAttributes
	 */
	public function setDelayAttributes($delayAttributes) {
		$this->delayAttributes = array_merge($this->delayAttributes, $delayAttributes);
	}

}