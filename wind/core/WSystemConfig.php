<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 配置信息解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WSystemConfig extends WConfig implements WContext{
	private $system = array();
	private $custom = array();
	private $config = array();
	
	/**
	 * 争对数组格式的解析
	 * @param array $configSystem
	 * @param array $configCustom
	 */
	public function parse($configSystem,$configCustom = array()){
		if(!is_array($configSystem) || !is_array($configCustom))
			throw new WSystemException("配置信息格式不对",5);
		if(empty($configSystem))
			throw new WSystemException("系统配置不能为空",5);
		$this->config = array_merge($configSystem,$configCustom);
		$this->system  = $configSystem;
		$this->custom  = $configCustom;
	}
	
	/**
	 * 争对xml格式配置的解析
	 * @param xml $configSystem
	 * @param xml $configCustom
	 */
	public function parseXML($configSystem,$configCustom = null){
		
	}
	
	/**
	 * 根据配置名取得相应的配置
	 * @param string $configName
	 * @return string
	 */
	public function getConfig($configName){
		if($configName && isset($this->config[$configName])){
			return $this->config[$configName];
		}else{
			throw new WSystemException("{$configName}配置信息不存在",5);
		}
	}
	
	public function getRouterConfig(){
		if(isset($this->config['router'])){
			return $this->config['router'];
		}else{
			throw new WSystemException("路由配置信息不存在",5);
		}
	}
	
	public static function getInstance(){
		return W::getInstance(__CLASS__);
	}
}

?>