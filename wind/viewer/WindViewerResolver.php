<?php
Wind::import('WIND:viewer.IWindViewerResolver');
Wind::import('WIND:viewer.exception.WindViewException');

/**
 * 默认视图引擎
 * 基于URL的视图引擎，视图名和模板名称保持一致
 * 
 * 该视图类接收一个modelAndView对象，通过解析该对象获得一个逻辑视图名称
 * 并将该逻辑视图名称，映射到具体的视图资源。
 * 
 * 布局解析，模板解析，编译缓存，模板缓存
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindViewerResolver extends WindModule implements IWindViewerResolver {
	/**
	 * @var array
	 */
	protected $vars = array();
	/**
	 * @var WindView
	 */
	protected $windView = null;
	
	/**
	 * @var WindLayout
	 */
	protected $windLayout = null;

	/**
	 * @param WindView $windView
	 */
	public function __construct($windView = null) {
		$this->windView = $windView;
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windFetch()
	 */
	public function windFetch($template = '') {
		ob_start();
		if (!$template) {
			$template = $this->windView->templateName;
			$templateFilePath = $this->windView->getViewTemplate($template);
			$compileFilePath = $this->windView->getCompileFile($template);
			if ($this->checkReCompile($templateFilePath, $compileFilePath)) {
				$layout = $this->getWindLayout();
				$layout->setLayout($this->windView->layout);
				$layout->setTheme($this->windView->theme);
				WindFile::write($compileFilePath, $layout->parser($this));
			}
		} else
			list($compileFilePath) = $this->compile($template);
		
		WindRender::render($compileFilePath, Wind::getApp()->getResponse()->getData($template), $this);
		return ob_get_clean();
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windAssign()
	 */
	public function windAssign($vars, $key = '') {}

	/**
	 * 编译模板并返回编译后模板名称,
	 * $output==true： 直接返回编译结果,不将结果写入编译文件中
	 * $output==false：返回编译文件地址
	 * 
	 * @param string $template
	 * @param string $suffix
	 * @param boolean $output
	 * @return string
	 */
	public function compile($template, $suffix = '', $output = false) {
		$templateFile = $this->windView->getViewTemplate($template, $suffix);
		if (!is_file($templateFile))
			throw new WindViewException('[component.viewer.WindViewerResolver.compile] ' . $templateFile, 
				WindViewException::VIEW_NOT_EXIST);
		
		$compileFile = $this->windView->getCompileFile($template);
		if (!$this->checkReCompile())
			return array($compileFile, '');
			/* @var $_windTemplate WindViewTemplate */
		$_windTemplate = Wind::getApp()->getWindFactory()->getInstance('template');
		$_output = $_windTemplate->compile($templateFile, $this);
		if ($output === false) {
			WindFile::write($compileFile, $_output);
		}
		return array($compileFile, $_output);
	}

	/**
	 * 检查是否需要重新编译,需要编译返回false，不需要编译返回true
	 * 
	 * @param string $templateFilePath
	 * @param string $compileFilePath
	 * @return boolean
	 */
	private function checkReCompile($templateFilePath, $compileFilePath) {
		if (WIND_DEBUG) {return true;}
		if ($this->getWindView()->isCompile) {
			$_c_m_t = @filemtime($compileFilePath);
			if ((int) $_c_m_t <= (int) @filemtime($templateFilePath))
				return true;
		}
		return false;
	}

	/**
	 * @return WindView
	 */
	public function getWindView() {
		return $this->windView;
	}

	/**
	 * @param WindView $windView
	 */
	public function setWindView($windView) {
		$this->windView = $windView;
	}

	/**
	 * @return WindLayout
	 */
	public function getWindLayout() {
		return $this->_getWindLayout('', $this);
	}
}

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindRender {

	/**
	 * @param string $__tpl
	 * @param array $__vars
	 * @param WindViewerResolver $__viewer
	 * @throws WindViewException
	 */
	public static function render($__tpl, $__vars, $__viewer) {
		$__theme = $__viewer->getWindView()->theme;
		$themeUrl = $__theme ? $__theme : Wind::getApp()->getRequest()->getBaseUrl(true);
		unset($__theme);
		@extract($__vars, EXTR_REFS);
		if (!@include_once ($__tpl))
			throw new WindViewException('[component.viewer.WindRender.render] template name ' . $__tpl, 
				WindViewException::VIEW_NOT_EXIST);
	}
}