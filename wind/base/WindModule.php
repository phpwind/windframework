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
	 * @var array
	 */
	protected $_config = array();
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
		if ($_prefix == '_get') {
			if (isset($this->delayAttributes[$_propertyName])) {
				$_property = $this->delayAttributes[$_propertyName];
				$_value = null;
				if (isset($_property['value'])) {
					$_value = $_property['value'];
				} elseif (isset($_property['ref'])) {
					$_value = $this->getSystemFactory()->getInstance($_property['ref'], $args);
				} elseif (isset($_property['path'])) {
					$_className = Wind::import($_property['path']);
					$_value = $this->getSystemFactory()->createInstance($_className, $args);
				}
				$this->$_propertyName = $_value;
				unset($this->delayAttributes[$_propertyName]);
			}
			return $this->$_propertyName;
		} elseif ($_prefix == '_set') {
			$this->$_propertyName = $args[0];
		}
	}

	/**
	 * 对象clone魔术方法
	 */
	public function __clone() {
		foreach ($this->writeTableCloneProperty() as $value) {
			if (!is_object($this->$value) || !isset($this->$value))
				continue;
			$this->$value = clone $this->$value;
		}
	}

	/**
	 * 返回该对象的数组类型
	 * 
	 * @return array
	 */
	public function toArray() {
		$reflection = new ReflectionClass(get_class($this));
		$properties = $reflection->getProperties();
		$_result = array();
		foreach ($properties as $property) {
			$_propertyName = $property->name;
			$_result[$_propertyName] = $this->$_propertyName;
		}
		return $_result;
	}

	/**
	 * 根据配置名取得相应的配置
	 * 
	 * @param string $configName 键名
	 * @param string $subConfigName 二级键名
	 * @param string $default 默认值
	 * @param array $config 
	 * @return string|array
	 */
	public function getConfig($configName = '', $subConfigName = '', $default = '', $config = array()) {
		if (empty($config))
			$config = $this->_config;
		if ($configName === '')
			return $config;
		if (!isset($config[$configName]))
			return $default;
		if ($subConfigName === '')
			return $config[$configName];
		if (!isset($config[$configName][$subConfigName]))
			return $default;
		return $config[$configName][$subConfigName];
	}

	/**
	 * Config配置,如果配置信息已经存在，则会合并配置
	 * 
	 * @param string|array|windConfig $config
	 * @return
	 */
	public function setConfig($config) {
		if ($config) {
			if (is_string($config))
				$config = Wind::getApp()->getComponent('configParser')->parse($config);
			if (!empty($this->_config)) {
				$this->_config = array_merge($this->_config, (array) $config);
			} else
				$this->_config = $config;
		}
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