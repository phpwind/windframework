<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindFactory {
	private static $instance = array();
	
	abstract public function create();
	
	static public function &getFactory($class) {
		if (!$class) return null;
		if (!isset(self::$instance[$class]) || self::$instance[$class] === null) {
			self::$instance[$class] = &new $class();
		}
		return self::$instance[$class];
	}
}