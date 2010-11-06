<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WSystemConfig extends WModule implements WContext {
	
	function parse($config = array(), $ifRecursion = false) {
		$args = func_get_args();
		$obj = isset($args[2]) ? $args[2] : $this;
		if (empty($args[2])) {
			$systemConfig = $this->getSystemConfig();
			$config = array_merge($systemConfig, $config);
		}
		foreach ($config as $key => $value) {
			if ($ifRecursion && is_array($value)) {
				$obj->{$key} = new stdClass();
				$obj->parse($value, $ifRecursion, $this->{$key});
			} else {
				$obj->{$key} = $value;
			}
		}
	}
	
	/**
	 * @return WSystemConfig
	 */
	public static function getInstance() {
		return W::getInstance(__CLASS__);
	}

}