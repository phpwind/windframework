<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WViewFactory {
	
	private $viewPath = 'template';
	private $ext = 'htm';
	private $config = array();
	
	private $forward = '';
	
	const VIEW_CONFIG = 'view';
	
	private static $instance = null;
	
	protected function __construct($configObj = null) {
		$this->initConfig($configObj);
	}
	
	public function setForward($forward) {
		$this->forward;
	}
	
	/**
	 * 初始化配置文件，获得模板路径信息
	 * 
	 * @param WSystemConfig $configObj
	 */
	private function initConfig(WSystemConfig $configObj) {
		if ($configObj == null)
			return;
		
		$this->config = $configObj->getConfig(self::VIEW_CONFIG);
		if (isset($this->config['viewPath']))
			$this->viewPath = $this->config['viewPath'];
		
		if (isset($this->config['ext']))
			$this->ext = $this->config['ext'];
	}
	
	/**
	 * 根据模板名称获得模板文件
	 * 
	 * @param string $viewName
	 * @return string
	 */
	private function getViewTemplate($viewName) {
		return '';
	}
	
	/**
	 * 根据视图forward的逻辑视图名称获得真是的视图文件名
	 * @return string
	 */
	private function getViewFileName() {
		if (!$this->forward)
			$this->forward = 'index';
		
		return $this->forward . $this->ext;
	}
	
	/**
	 * @param WSystemConfig $configObj
	 * @return WViewFactory
	 */
	static public function getInstance(WSystemConfig $configObj = null) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($configObj);
		}
		return self::$instance;
	}
}