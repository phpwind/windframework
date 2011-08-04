<?php
/**
 * 处理视图请求的准备工作，并将视图请求提交给某一个具体的视图解析器
 * 如果视图请求是一个重定向请求，或者是请求另一个操作
 * 则返回一个forward对象
 * <config>
 * <template-dir value='template' />
 * <template-ext value='htm' />
 * <is-cache value='true' />
 * <cache-dir value='cache' />
 * <compile-dir value='compile.template' />
 * </config>
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindView extends WindModule {
	/**
	 * 模板路径信息
	 *
	 * @var string
	 */
	protected $templateDir;
	/**
	 * 模板文件的扩展名
	 *
	 * @var string
	 */
	protected $templateExt;
	/**
	 * 模板名称
	 *
	 * @var string
	 */
	protected $templateName;
	/**
	 * 是否进行视图缓存
	 *
	 * @var boolean
	 */
	protected $isCache = false;
	
	/**
	 * 缓存策略
	 *
	 * @var string
	 */
	protected $cacheClass;
	
	/**
	 * 是否对模板变量进行html字符过滤
	 * 
	 * @var boolean
	 */
	protected $htmlspecialchars = true;
	/**
	 * 编译目录
	 *
	 * @var string
	 */
	protected $compileDir;
	/**
	 * 视图解析引擎
	 *
	 * @var WindViewerResolver
	 */
	protected $viewResolver = null;
	/**
	 * 试图缓存
	 * @var AbstractWindCache
	 */
	protected $viewCache = null;
	/**
	 * 布局文件
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * 视图渲染
	 * 
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 */
	public function render($forward, $router, $display = false) {
		$this->init();
		if (!($_templateName = $forward->getTemplateName())) {
			Wind::log('[component.viewer.WindView.render] view render fail. TemplateName is not defined.', 
				WindLogger::LEVEL_DEBUG, 'wind.component');
			return;
		}
		$this->setTemplateName($_templateName);
		if ($_ext = $forward->getTemplateExt()) $this->setTemplateExt($_ext);
		if ($_path = $forward->getTemplatePath()) $this->setTemplateDir($_path);
		if ($_layout = $forward->getLayout()) $this->setLayout($_layout);
		
		$viewResolver = $this->getViewResolver();
		$viewResolver->windAssign($forward->getVars(), $_templateName);
		if ($display === false) {
			$this->getResponse()->setBody($viewResolver->windFetch(), $_templateName);
		} else
			$viewResolver->displayWindFetch();
	}

	/**
	 * 模板路径解析
	 * 根据模板的逻辑名称，返回模板的绝对路径信息
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	public function getViewTemplate($template = '', $ext = '') {
		return $this->parseFilePath($template, $ext, $this->getTemplateDir());
	}

	/**
	 * 模板编译路径解析
	 * 根据模板的逻辑名称，返回模板的绝对路径信息
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	public function getCompileFile($template = '', $ext = '') {
		return $this->parseFilePath($template, $ext, $this->getCompileDir(), true);
	}

	/**
	 * @param $fileName
	 * @param $fileExt
	 * @param $path
	 */
	private function parseFilePath($fileName, $fileExt, $path, $ifCheckPath = false) {
		if (!$fileName) $fileName = $this->getTemplateName();
		if (!$fileExt) $fileExt = $this->getTemplateExt();
		if (strrpos($path, ':') === false) $path = Wind::getAppName() . ':' . $path;
		if ($ifCheckPath) {
			$dir = Wind::getRealDir($path);
			if (!is_dir($dir)) {
				@mkdir($dir);
				Wind::log('[component.viewer.WindView.parseFilePath] template dir is not exist.', 
					WindLogger::LEVEL_DEBUG, 'wind.component');
			}
		}
		return Wind::getRealPath($path . '.' . $fileName, $fileExt);
	}

	/**
	 * 初始哈windView类
	 * 
	 * @return
	 */
	public function init() {
		$this->setTemplateDir($this->getConfig('template-dir'));
		$this->setCompileDir($this->getConfig('compile-dir'));
		$this->setTemplateExt($this->getConfig('template-ext'));
		$this->setIsCache($this->getConfig('is-cache'));
		$this->setCacheClass($this->getConfig('cache-class'));
	}

	/**
	 * @return string
	 */
	public function getTemplateDir() {
		return $this->templateDir;
	}

	/**
	 * @return string
	 */
	public function getCompileDir() {
		return $this->compileDir;
	}

	/**
	 * @return string
	 */
	public function getTemplateExt() {
		return $this->templateExt;
	}

	/**
	 * @return string
	 */
	public function getTemplateName() {
		return $this->templateName;
	}
	
	/**
	 * @return boolean
	 */
	public function getIsCache() {
		return $this->isCache;
	}

	/**
	 * @return WindLayout
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * @return string
	 */
	public function getCacheClass() {
		return $this->cacheClass;
	}

	/**
	 * @param string $templateDir
	 */
	public function setTemplateDir($templateDir) {
		$this->templateDir = $templateDir;
	}

	/**
	 * @param string $compileDir
	 */
	public function setCompileDir($compileDir) {
		$this->compileDir = $compileDir;
	}

	/**
	 * @param string $templateExt
	 */
	public function setTemplateExt($templateExt) {
		$this->templateExt = $templateExt;
	}

	/**
	 * @param string $templateName
	 */
	public function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}

	/**
	 * @param boolean $isCache
	 */
	public function setIsCache($isCache) {
		$this->isCache = (strtolower($isCache) == 'true' || $isCache == '1');
	}

	/**
	 * @param string $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * @param string $class
	 */
	public function setCacheClass($class) {
		$this->cacheClass = $class;
	}

	/**
	 * @return the $htmlspecialchars
	 */
	public function getHtmlspecialchars() {
		return $this->htmlspecialchars;
	}

	/**
	 * @param boolean $htmlspecialchars
	 */
	public function setHtmlspecialchars($htmlspecialchars) {
		$this->htmlspecialchars = $htmlspecialchars;
	}
	
	/**
	 * @return WindViewerCache
	 */
	public function getViewCache() {
		if ($this->viewCache === null) {
			$this->_getViewCache();
		}
		return $this->viewCache;
	}
	
	/**
	 * @param AbstractWindCache $viewCache
	 */
	public function setViewCache($viewCache) {
		$this->viewCache = $viewCache;
	}

	/**
	 * @return WindViewerResolver
	 */
	public function getViewResolver() {
		if (null !== $this->viewResolver) return $this->viewResolver;
		$this->_getViewResolver();
		$this->viewResolver->setWindView($this);
		if (!$this->getIsCache()) return $this->viewResolver;
		$this->viewResolver = new WindClassProxy($this->viewResolver);
		$listener = Wind::import('COM:viewer.listener.WindViewCacheListener');
		$this->viewResolver->registerEventListener('windFetch', new $listener($this));
		return $this->viewResolver;
	}

	/**
	 * @param WindViewerResolver $viewResolver
	 */
	public function setViewResolver($viewResolver) {
		$this->viewResolver = $viewResolver;
	}

}