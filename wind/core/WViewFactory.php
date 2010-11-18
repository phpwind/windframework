<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 根据视图的配置信息返回视图引擎对象的引用
 *
 * 根据视图的逻辑名称，返回视图模板地址
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
	
	private $viewPath = 'templates';
	private $tpl = 'index';
	private $ext = 'phtml';
	private $engine = 'default';
	
	/* 配置信息 */
	private $engines = array();
	private $config = array();
	
	private $viewer = null;
	private static $instance = null;
	
	protected function __construct() {
		$this->initConfig();
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
			$object = new $className();
			$object->setTpl($tpl);
			($this->config['compileDir']) && $object->setCompileDir($this->config['compileDir']);
			($this->config['cacheDir']) && $object->setCacheDir($this->config['cacheDir']);
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
		if (!is_array($this->viewPath))
			$this->viewPath = array($this->viewPath);
		foreach ($this->viewPath as $key => $value) {
			$realPath = W::getRealPath($value . W::getSeparator() . $this->tpl, $this->ext);
			if ($realPath)
				break;
		}
		if (!$realPath)
			throw new WException('template file is not exist.');
		
		return $realPath ? $realPath : null;
	}
	
	/**
	 * 初始化配置文件，获得模板路径信息
	 * 
	 */
	private function initConfig() {
		$configObj = W::getInstance('WSystemConfig');
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
	 * @return WViewFactory
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
}