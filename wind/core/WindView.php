<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 处理视图请求的准备工作，并将视图请求提交给某一个具体的视图解析器
 * 如果视图请求是一个重定向请求，或者是请求另一个操作
 * 则返回一个forward对象
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindView {
	public $templateDir;
	public $templateDefault;
	public $templateExt;
	public $templateCacheDir;
	public $templateCompileDir;
	public $layout = null;
	
	private $config = array();
	private $viewResolver = null;
	
	/**
	 * @param string $templateName
	 * @param WindForward $forward
	 */
	public function __construct($templateConfig = '') {
		$this->parseConfig($templateConfig);
	}
	
	/**
	 * 返回视图解析器对象
	 * 
	 * @return WindViewer
	 */
	public function createViewerResolver() {
		if ($this->viewResolver === null) {
			$viewerResolver = C::getViewerResolvers($this->config[IWindConfig::TEMPLATE_RESOLVER]);
			$className = L::import($viewerResolver[IWindConfig::ROUTER_PARSERS_CLASS]);
			if (!class_exists($className)) {
				throw new WindException('viewer resolver ' . $className . ' is not exists in ' . $viewerResolver);
			}
			$object = new $className();
			$this->viewResolver = &$object;
		}
		$this->viewResolver->initWithView($this);
		return $this->viewResolver;
	}
	
	/**
	 * 通过WindForward视图信息设置view
	 * @param WindForward $forward
	 */
	public function initViewWithForward($forward) {
		if ($forward->getTemplateName()) $this->templateDefault = $forward->getTemplateName();
		if ($forward->getTemplatePath()) $this->templateDir = $forward->getTemplatePath();
		$this->layout = $forward->getLayout();
		return $this;
	}
	
	/**
	 * @param string $actionHandle
	 */
	public function doAction($actionHandle = '', $path = '') {
		if (!($this->dispatcher instanceof WindDispatcher)) throw new WindException('do action error.');
		$forward = new WindForward();
		$forward->setAction($actionHandle, $path);
		$this->dispatcher->setForward($forward)->dispatch(true);
	}
	
	/**
	 * 初始化配置文件，获得模板路径信息
	 * <dir>template</dir>
	 * <default>index</default>
	 * <ext>htm</ext>
	 * <resolver>default</resolver>
	 * <isCache>false</isCache>
	 * <cacheDir>cache</cacheDir>
	 * <compileDir>compile</compileDir>
	 */
	private function parseConfig($templateConfig) {
		$configs = C::getTemplate();
		if ($templateConfig) {
			if (!isset($configs[$templateConfig])) throw new WindException('the template config ' . $templateConfig . ' is not exists.');
			$this->config = $configs[$templateConfig];
		} elseif (count($configs) >= 1)
			$this->config = array_pop($configs);
		else
			throw new WindException('parse template config error.');
		foreach ($this->config as $key => $value) {
			$property = 'template' . ucfirst($key);
			$this->$property = $value;
		}
	}

}