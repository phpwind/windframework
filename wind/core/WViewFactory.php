<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 根据视图的逻辑名称，返回视图模板内容
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WViewFactory {
	
	/* 视图配置信息  */
	const VIEW_CONFIG = 'view';
	const VIEW_ENGINE = 'viewEngine';
	
	private $viewPath = 'template';
	private $ext = 'htm';
	private $tpl = 'index';
	private $engine = 'default';
	private $engines = array();
	private $config = array();
	
	private $viewer = null;
	private static $instance = null;
	
	protected function __construct($configObj = null) {
		$this->initConfig($configObj);
	}
	
	/**
	 * 返回视图对象
	 * 
	 * @return WViewer
	 */
	public function create($tpl = '') {
		if ($this->viewer === null) {
			if (!($enginePath = $this->engines[$this->engine]))
				throw new WException('Template engine ' . $this->engine . ' is not exists.');
			if (!($tpl = $this->getViewTemplate($tpl)))
				throw new WException('Template file ' . $this->tpl . ' is not exists.');
			
			W::import($enginePath);
			$className = substr($enginePath, strrpos($enginePath, '.') + 1);
			$class = new ReflectionClass($className);
			$object = call_user_func_array(array(
				$class, 
				'newInstance'
			), array(
				$tpl
			));
			$this->viewer = &$object;
		}
		return $this->viewer;
	}
	
	/**
	 * 根据模板名称获得模板文件
	 * 模板文件名称|扩展名|绝对路径信息
	 * 
	 * @param string $viewName
	 * @return array()
	 */
	private function getViewTemplate($tpl = null) {
		$tpl && $this->tpl = $tpl;
		
		return W::getRealPath($this->viewPath . '.' . $this->tpl, $this->ext);
	}
	
	/**
	 * 初始化配置文件，获得模板路径信息
	 * 
	 * @param WSystemConfig $configObj
	 */
	private function initConfig(WSystemConfig $configObj) {
		if ($configObj == null)
			return;
		
		$this->engines = $configObj->getConfig(self::VIEW_ENGINE);
		$this->config = $configObj->getConfig(self::VIEW_CONFIG);
		
		if (isset($this->config['viewPath']))
			$this->viewPath = $this->config['viewPath'];
		
		if (isset($this->config['ext']))
			$this->ext = $this->config['ext'];
		
		if (isset($this->config['tpl']))
			$this->tpl = $this->config['tpl'];
		
		if (isset($this->config['engine']))
			$this->engine = $this->config['engine'];
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