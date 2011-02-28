<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindFactory {

	protected static $instance = null;

	/**
	 * 抽象工厂方法
	 * @return object
	 */
	static public function getFactory($class = '') {
		if (self::$instance === null && $class !== '') {
			self::$instance = new $class();
		}
		return self::$instance;
	}
}

?>