<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('base.WModel');
/**
 * 对配置文件的解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WConfigParser {

	public function parse($config = array(),$ifGlobal = true,$ifSelf = true){
		$config = $config ? $config : $this->getAppConfig();
		$config = $ifGlobal ? array_merge($this->getSystemConfig(),$config) : $config;
		$obj = $ifSelf ? $this : new stdClass();
		foreach($config as $key=>$value) $obj->{$key} = $value;
		return  $obj;
	}
	
	
	private function getSystemConfig(){
		return include 'config.php';
	}
	
	private function getAppConfig(){
		$path = WIND_PATH;
		$pos = strrpos($path,DIRECTORY_SEPARATOR);
		if($post+1 == strlen($path)) {
			$path = rtrim($path,DIRECTORY_SEPARATOR);
			$pos = strrpos($path,DIRECTORY_SEPARATOR);
		}
		$path = substr($path,0,$pos+1);
		$appConfigFile = $path.'config.php';
		return is_file($appConfigFile) ? include $appConfigFile : array();
	}
	
}

?>