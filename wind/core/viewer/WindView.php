<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindComponentModule');
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
class WindView extends WindComponentModule {

	const TEMPLATE_DIR = 'template-dir';

	const TEMPLATE_EXT = 'template-ext';

	const IS_CACHE = 'is-cache';

	const CACHE_DIR = 'cache-dir';

	const COMPILE_DIR = 'compile-dir';

	const SHARE_VAR = 'share-var';

	protected $templatePath = '';

	protected $templateExt = '';

	protected $templateName = '';

	protected $isCache = '';

	protected $compileDir = '';

	protected $cacheDir = '';

	protected $shareVar = '';

	/**
	 * 视图解析引擎
	 *
	 * @var WindViewerResolver
	 */
	protected $viewResolver = null;

	/**
	 * 布局文件
	 *
	 * @var string
	 */
	protected $layout = '';

	/**
	 * 设置布局对象
	 * @param WindLayout|string $layout
	 */
	public function setLayout($layout) {
		if ($layout instanceof WindLayout)
			$this->layout = $layout->getLayoutFile();
		else
			$this->layout = $layout;
	
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
		$path = $this->getTemplatePath();
		return $this->parseFilePath($template, $ext, $path);
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
		$path = $this->getCompileDir();
		return $this->parseFilePath($template, $ext, $path);
	}

	/**
	 * 模板缓存路径路径解析
	 * 根据模板的逻辑名称，返回模板的绝对路径信息
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	public function getCacheFile($template = '', $ext = '') {
		$path = $this->getCacheDir();
		return $this->parseFilePath($template, $ext, $path);
	}

	/**
	 * @return WindViewerResolver
	 */
	public function getViewResolver() {
		if ($this->viewResolver !== null) {
			if ($this->getIsCache() === 'true') {
				$this->viewResolver->setClassProxy(new WindClassProxy());
				$this->viewResolver = $this->viewResolver->getClassProxy();
				$this->viewResolver->registerEventListener('windFetch', new WindViewCacheListener($this));
			}
			$this->viewResolver->setWindView($this);
			return $this->viewResolver;
		} else {
			throw new WindException('getViewResolver()', WindException::ERROR_RETURN_TYPE_ERROR);
		}
	}

	/**
	 * @param $fileName
	 * @param $fileExt
	 * @param $path
	 */
	private function parseFilePath($fileName, $fileExt, $path) {
		if (!$fileName) $fileName = $this->getTemplateName();
		if (!$fileExt) $fileExt = $this->getTemplateExt();
		if (!$fileName) return '';
		if (strrpos($path, ':') === false) $path = $this->windSystemConfig->getAppName() . ':' . $path;
		if (!($dir = L::getRealPath($path, true)) || !is_dir($dir)) {
			throw new WindException('The file folder \'' . $dir . '\' is not exist.');
		}
		return L::getRealPath($path . '.' . $fileName . '.' . $fileExt);
	}

	/**
	 * @return string $templatePath
	 */
	public function getTemplatePath() {
		//TODO change templatePath to templateDir
		if ($this->templatePath === '') {
			$this->templatePath = $this->getDefaultValue(self::TEMPLATE_DIR);
		}
		return $this->templatePath;
	}

	/**
	 * @return string $templateExt
	 */
	public function getTemplateExt() {
		if ($this->templateExt === '') {
			$this->templateExt = $this->getDefaultValue(self::TEMPLATE_EXT);
		}
		return $this->templateExt;
	}

	/**
	 * @return boolean $isCache
	 */
	public function getIsCache() {
		if ($this->isCache === '') {
			$this->isCache = $this->getDefaultValue(self::IS_CACHE);
		}
		return $this->isCache;
	}

	/**
	 * @return string $compileDir
	 */
	public function getCompileDir() {
		if ($this->compileDir === '') {
			$this->compileDir = $this->getDefaultValue(self::COMPILE_DIR);
		}
		return $this->compileDir;
	}

	/**
	 * @return string $cacheDir
	 */
	public function getCacheDir() {
		if ($this->cacheDir === '') $this->cacheDir = $this->getDefaultValue(self::CACHE_DIR);
		return $this->cacheDir;
	}

	/**
	 * @return boolean $shareVar
	 */
	public function getShareVar() {
		if ($this->shareVar === '') $this->shareVar = $this->getDefaultValue(self::SHARE_VAR);
		return $this->shareVar;
	}

	/**
	 * @param string $type
	 */
	private function getDefaultValue($type) {
		return $this->getConfig()->getConfig($type, WindSystemConfig::VALUE);
	}

	/* (non-PHPdoc)
	 * @see WindModule::getWriteTableForGetterAndSetter()
	 */
	protected function getWriteTableForGetterAndSetter() {
		return array('templatePath', 'templateExt', 'templateName', 'isCache', 'compileDir', 'cacheDir', 'viewResolver', 
			'layout');
	}

	/* (non-PHPdoc)
	 * @see WindModule::getCloneProperty()
	 */
	protected function getCloneProperty() {
		return array('viewResolver');
	}

}