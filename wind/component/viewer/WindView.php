<?php
Wind::import('COM:viewer.IWindView');
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
class WindView extends WindModule implements IWindView {
	/**
	 * 模板路径信息
	 *
	 * @var string
	 */
	public $templateDir;
	/**
	 * 模板文件的扩展名
	 *
	 * @var string
	 */
	public $templateExt;
	/**
	 * 模板名称
	 *
	 * @var string
	 */
	public $templateName;
	/**
	 * 是否对模板变量进行html字符过滤
	 * @var boolean
	 */
	public $htmlspecialchars = true;
	/**
	 * 是否开启模板编译
	 * 00: 0   关闭,不进行模板编译
	 * 01: 1  进行模板编译
	 * @var boolean
	 */
	public $isCompile = 0;
	/**
	 * 编译目录
	 * @var string
	 */
	public $compileDir;
	/**
	 * 编译脚本后缀
	 * @var string
	 */
	public $compileExt = 'tpl';
	
	/**
	 * 视图解析引擎
	 * @var WindViewerResolver
	 */
	protected $viewResolver = null;
	/**
	 * 布局文件
	 * @var string
	 */
	protected $layout;

	/**
	 * 视图渲染
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 */
	public function render($forward, $router, $display = false) {
		$this->templateName = $forward->getTemplateName();
		if (!$this->templateName)
			return;
		if (null !== ($_ext = $forward->getTemplateExt()))
			$this->templateExt = $_ext;
		if (null !== ($_path = $forward->getTemplatePath()))
			$this->templateDir = $_path;
		if (null !== ($_layout = $forward->getLayout()))
			$this->layout = $_layout;
		
		$viewResolver = $this->_getViewResolver($this);
		$viewResolver->windAssign($forward->getVars(), $this->templateName);
		if ($display === false) {
			$this->getResponse()->setBody($viewResolver->windFetch(), $this->templateName);
		} else
			$viewResolver->displayWindFetch();
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($this->_config) {
			$this->templateDir = $this->getConfig('template-dir', '', $this->templateDir);
			$this->templateExt = $this->getConfig('template-ext', '', $this->templateExt);
			$this->compileDir = $this->getConfig('compile-dir', '', $this->compileDir);
			$this->compileExt = $this->getConfig('compile-ext', '', $this->compileExt);
			$this->isCompile = $this->getConfig('is-compile', '', $this->isCompile);
			$this->htmlspecialchars = $this->getConfig('htmlspecialchars', '', 
				$this->htmlspecialchars);
		}
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
		if (!$template) {
			$template = $this->templateName;
		}
		/* elseif (is_file($template))
			return $template; */
		!$ext && $ext = $this->templateExt;
		return Wind::getRealPath($this->templateDir . '.' . $template, ($ext ? $ext : false));
	}

	/**
	 * 模板编译路径解析
	 * 根据模板的逻辑名称，返回模板的绝对路径信息
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	public function getCompileFile($template = '') {
		if (!$this->compileDir)
			return;
		if (!$template) {
			$template = $this->templateName;
		} 
		/*elseif (is_file($template)) {
			$_info = pathinfo($template);
			$template = $_info['filename'];
		}*/
		$dir = Wind::getRealDir($this->compileDir);
		if (!is_dir($dir))
			throw new WindViewException(
				'[component.viewer.WindView.getCompileFile] Template compile dir is not exist.');
		$_tmp = explode('.', $template);
		foreach ($_tmp as $_dir) {
			!is_dir($dir) && @mkdir($dir);
			$dir .= DIRECTORY_SEPARATOR . $_dir;
		}
		return $this->compileExt ? $dir . '.' . $this->compileExt : $dir;
	}

	/**
	 * @return WindViewerResolver
	 */
	public function getViewResolver() {
		if (null !== $this->viewResolver)
			return $this->viewResolver;
		$this->_getViewResolver();
		$this->viewResolver->setWindView($this);
		if (!$this->getIsCache())
			return $this->viewResolver;
		$this->viewResolver = new WindClassProxy($this->viewResolver);
		$listener = Wind::import('COM:viewer.listener.WindViewCacheListener');
		$this->viewResolver->registerEventListener('windFetch', new $listener($this));
		return $this->viewResolver;
	}

	/**
	 * @return the $layout
	 */
	public function getLayout() {
		return $this->layout;
	}

}