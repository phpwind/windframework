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
	private $templatePath;
	private $templateName;
	private $templateExt;
	
	private $templateCacheDir;
	private $templateCompileDir;
	
	private $isCache;
	private $reolver;
	
	private $forward = null;
	
	/**
	 * @param string $templateName
	 * @param WindForward $forward
	 */
	public function __construct($templateName = '', $forward = null) {
		$this->initConfig();
		if ($templateName) $this->templateName = $templateName;
		$this->setViewWithForward($forward);
	}
	
	/**
	 * 返回视图解析器对象
	 * 
	 * @return WindViewer
	 */
	public function &createViewerResolver() {
		$viewerResolver = C::getViewerResolvers($this->reolver);
		list(, $className, , $viewerResolver) = L::getRealPath($viewerResolver, true);
		L::import($viewerResolver);
		if (!class_exists($className)) {
			throw new WindException('viewer resolver ' . $className . ' is not exists in ' . $viewerResolver);
		}
		$object = new $className();
		$object->initWithView($this);
		return $object;
	}
	
	/**
	 * 初始化配置文件，获得模板路径信息
	 */
	private function initConfig() {
		$this->templatePath = C::getTemplate(IWindConfig::TEMPLATE_PATH);
		$this->templateName = C::getTemplate(IWindConfig::TEMPLATE_NAME);
		$this->templateCacheDir = C::getTemplate(IWindConfig::TEMPLATE_CACHE_DIR);
		$this->templateCompileDir = C::getTemplate(IWindConfig::TEMPLATE_COMPILER_DIR);
		$this->templateExt = C::getTemplate(IWindConfig::TEMPLATE_EXT);
		$this->isCache = C::getTemplate(IWindConfig::TEMPLATE_ISCACHE);
		$this->reolver = C::getTemplate(IWindConfig::TEMPLATE_RESOLVER);
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
		$this->forward = $forward;
		$this->templateName = $this->getForward()->getViewName();
		if ($this->getForward()->getPath()) {
			$this->templatePath = $this->getForward()->getPath();
		}
	}
	
	/**
	 * @return the $templatePath
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}
	
	/**
	 * @return the $templateName
	 */
	public function getTemplateName() {
		return $this->templateName;
	}
	
	/**
	 * @return the $templateExt
	 */
	public function getTemplateExt() {
		return $this->templateExt;
	}
	
	/**
	 * @return the $isCache
	 */
	public function getIsCache() {
		return $this->isCache;
	}
	
	/**
	 * @param $templatePath the $templatePath to set
	 * @author Qiong Wu
	 */
	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
	}
	
	/**
	 * @param $templateName the $templateName to set
	 * @author Qiong Wu
	 */
	public function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}
	
	/**
	 * @param $templateExt the $templateExt to set
	 * @author Qiong Wu
	 */
	public function setTemplateExt($templateExt) {
		$this->templateExt = $templateExt;
	}
	
	/**
	 * @return WindForward
	 */
	public function getForward() {
		return $this->forward;
	}
	
	/**
	 * @return the $templateCacheDir
	 */
	public function getTemplateCacheDir() {
		return $this->templateCacheDir;
	}
	
	/**
	 * @return the $templateCompileDir
	 */
	public function getTemplateCompileDir() {
		return $this->templateCompileDir;
	}
	
	/**
	 * @param $templateCacheDir the $templateCacheDir to set
	 * @author Qiong Wu
	 */
	public function setTemplateCacheDir($templateCacheDir) {
		$this->templateCacheDir = $templateCacheDir;
	}
	
	/**
	 * @param $templateCompileDir the $templateCompileDir to set
	 * @author Qiong Wu
	 */
	public function setTemplateCompileDir($templateCompileDir) {
		$this->templateCompileDir = $templateCompileDir;
	}

}