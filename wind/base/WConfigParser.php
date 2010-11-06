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
class WConfigParser {
	
	public function __construct($config = array(),$ifRecursion = false){
		$this->parse($config,$ifRecursion);
	}
	
	private function parse($config = array(),$ifRecursion = false){
		$args = func_get_args();
		$obj = isset($args[2]) ? $args[2] : $this;
		if(empty($args[2])){
			$systemConfig = $this->getSystemConfig();
			$config = array_merge($systemConfig,$config);
		}
		foreach($config as $key=>$value){
			if($ifRecursion && is_array($value)){
				$obj->{$key} = new stdClass();
				$obj->parse($value,$ifRecursion,$this->{$key});
			}else{
				$obj->{$key} = $value;
			}
		}
	}
	
	
	
	private function getSystemConfig(){
		return include 'config.php';
	}
}

?>