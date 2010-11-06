<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 对配置文件的解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WSystemConfig extends WModule implements WContext {
	private $systemConfig;
	private $config;
	
	/**
	 * 
	 * @param array $sytemConfig	//框架的默认配置
	 * @param array $config			//应用配置
	 */
	function parse($sytemConfig, $config = NULL) {
		
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
	 * xml 格式配置解析
	 */
	function parse1() {

	}
	
	function getRouterConfig() {
		return '';
	}
	
	/**
	 * 
	 */
	function getConfig($configName) {
		return '';
	}
	
	/**
	 * @return WSystemConfig
	 */
	public static function getInstance() {
		return W::getInstance(__CLASS__);
	}

}

?>