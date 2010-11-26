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
	private $templatePath = 'template';
	private $templateName = 'index';
	private $templateExt = 'htm';
	
	private $templateCacheDir = '';
	private $templateCompileDir = '';
	
	private $reolver = 'default';
	
	/**
	 * @var $this->mav WindModelAndView
	 */
	private $mav = null;
	
	/**
	 * @param string $templateName
	 * @param WindModelAndView $mav
	 */
	public function __construct($templateName = '', $mav = null) {
		$this->initConfig();
		if ($templateName) $this->templateName = $templateName;
		$this->mav = $mav ? $mav : new WindModelAndView($templateName);
	}
	
	/**
	 * 通过modelandview视图信息设置view
	 * @param WindModelAndView $mav
	 */
	public function setViewWithObject(&$mav) {
		if ($mav instanceof WindModelAndView) {
			$this->mav = $mav;
			$this->templateName = $this->mav->getViewName();
		}
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
		$this->reolver = C::getTemplate(IWindConfig::TEMPLATE_RESOLVER);
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
	 * @return the $reolver
	 */
	public function getReolver() {
		return $this->reolver;
	}
	
	/**
	 * @return the $viewerResolvers
	 */
	public function getViewerResolvers() {
		return $this->viewerResolvers;
	}
	
	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
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
	
	/**
	 * @return WindModelAndView $mav
	 */
	public function getMav() {
		return $this->mav;
	}

}