<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.WindModule');
/**
 * 框架核心组件基类
 * the last known user to change this file in the repository  <$LastChangedBy: yishuo $>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id: WindComponent.php 809 2010-12-22 11:28:28Z yishuo $
 * @package
 */
abstract class WindComponentModule extends WindModule {

	private $_attribute = array();

	private $_config = null;

	/**
	 * Enter description here ...
	 */
	public function getAttribute($alias = '') {
		if ($alias === '')
			return $this->_attribute;
		else
			return isset($this->_attribute[$alias]) ? $this->_attribute[$alias] : null;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $alias
	 * @param object $object
	 */
	public function setAttribute($alias, $object = null) {
		if (is_array($alias))
			$this->_attribute += $alias;
		elseif (is_string($alias))
			$this->_attribute[$alias] = $object;
	}

	/**
	 * @return WindConfig
	 */
	public function getConfig() {
		return $this->_config;
	}

	/**
	 * @param WindConfig $config
	 */
	public function setConfig($config) {
		$this->_config = $config;
	}

}