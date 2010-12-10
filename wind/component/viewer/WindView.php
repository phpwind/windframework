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
	private $config = array();
	private $forward = null;
	private $viewResolver = null;
	
	/**
	 * @param string $templateName
	 * @param WindForward $forward
	 */
	public function __construct($forward = null, $templateConfig = '') {
		$this->parseConfig($templateConfig);
		$this->setViewWithForward($forward);
	}
	
	/**
	 * 返回视图解析器对象
	 * 
	 * @return WindViewer
	 */
	public function createViewerResolver() {
		if ($this->viewResolver === null) {
			$viewerResolver = C::getViewerResolvers($this->config[IWindConfig::TEMPLATE_RESOLVER]);
			list($className, $viewerResolver) = L::getRealPath($viewerResolver, true);
			L::import($viewerResolver);
			if (!class_exists($className)) {
				throw new WindException('viewer resolver ' . $className . ' is not exists in ' . $viewerResolver);
			}
			$object = new $className();
			$object->initWithView($this);
			$this->viewResolver = &$object;
		}
		return $this->viewResolver;
	}
	
	/**
	 * 初始化配置文件，获得模板路径信息
	 */
	private function parseConfig($templateConfig) {
		$configs = C::getTemplate();
		if ($templateConfig) {
			if (!isset($configs[$templateConfig]))
				throw new WindException('the template config ' . $templateConfig . ' is not exists.');
			$this->config = $configs[$templateConfig];
		} elseif (count($configs) == 1)
			$this->config = array_pop($configs);
		else throw new WindException('parse template config error.');
	}
	
	/**
	 * @param string $actionHandle
	 */
	public function doAction($actionHandle = '', $path = '') {
		$forward = clone $this->getForward();
		$forward->setAction($actionHandle, $path);
		WindDispatcher::getInstance()->setForward($forward)->dispatch(true);
	}
	
	/**
	 * 通过WindForward视图信息设置view
	 * @param WindForward $forward
	 */
	private function setViewWithForward($forward) {
		if ($forward->getTemplateName())
			$this->config[IWindConfig::TEMPLATE_DEFAULT] = $forward->getTemplateName();
		if ($forward->getTemplatePath())
			$this->config[IWindConfig::TEMPLATE_DIR] = $forward->getTemplatePath();
		$this->forward = $forward;
	}
	
	/**
	 * @return the $templatePath
	 */
	public function getTemplateDir() {
		return $this->config[IWindConfig::TEMPLATE_DIR];
	}
	
	/**
	 * @return the $templateName
	 */
	public function getTemplateName() {
		return $this->config[IWindConfig::TEMPLATE_DEFAULT];
	}
	
	/**
	 * @return the $templateExt
	 */
	public function getTemplateExt() {
		return $this->config[IWindConfig::TEMPLATE_EXT];
	}
	
	/**
	 * @return the $templateCacheDir
	 */
	public function getTemplateCacheDir() {
		return $this->config[IWindConfig::TEMPLATE_CACHE_DIR];
	}
	
	/**
	 * @return the $templateCompileDir
	 */
	public function getTemplateCompileDir() {
		return $this->config[IWindConfig::TEMPLATE_COMPILER_DIR];
	}
	
	/**
	 * @return WindForward
	 */
	public function getForward() {
		return $this->forward;
	}

}