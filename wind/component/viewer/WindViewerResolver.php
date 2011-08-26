<?php
Wind::import('COM:viewer.IWindViewerResolver');
Wind::import('COM:viewer.exception.WindViewException');

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
class WindViewerResolver implements IWindViewerResolver {
	/**
	 * @var array
	 */
	protected $vars = array();
	/**
	 * @var WindView
	 */
	protected $windView = null;

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
		if (!$template) {
			$layout = $this->windView->getLayout();
			$template = $layout ? $layout : $this->windView->templateName;
		}
		ob_start();
		$this->render($template);
		return ob_get_clean();
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windAssign()
	 */
	public function windAssign($vars, $key = '') {
		if ($key === '')
			$key = $this->windView->templateName;
		$this->vars[$key] = $vars;
	}

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
			throw new WindViewException('[component.viewer.WindView.parseFilePath] ' . $templateFile, 
				WindViewException::VIEW_NOT_EXIST);
		
		$compileFile = $this->windView->getCompileFile($template);
		if (!$this->checkReCompile($templateFile, $compileFile))
			return array($compileFile, '');
			/* @var $_windTemplate WindViewTemplate */
		$_windTemplate = Wind::getApp()->getWindFactory()->getInstance('template');
		$_output = $_windTemplate->compile($templateFile, $this);
		if ($output === false) {
			$compileFile = $this->windView->getCompileFile($template);
			WindFile::write($compileFile, $_output);
		}
		return array($compileFile, $_output);
	}

	/**
	 * 加载视图模板文件
	 * @param template
	 */
	protected function render($template) {
		list($_tmp) = $this->compile($template);
		/*$_var = Wind::getApp()->getResponse()->getData('G');
		if (isset($this->vars[$template]))
			$_var += $this->vars[$template];*/
		@extract(@$this->vars[$template], EXTR_REFS);
		if (!@include ($_tmp)) {
			throw new WindViewException(
				'[component.viewer.ViewerResolver.render] template name ' . $template, 
				WindViewException::VIEW_NOT_EXIST);
		}
	}

	/**
	 * 检查是否需要重新编译,需要编译返回false，不需要编译返回true
	 * @param string $templateFile
	 * @param string $compileFile
	 * @return boolean
	 */
	private function checkReCompile($templateFile, $compileFile) {
		return WIND_DEBUG || $this->getWindView()->isCompile;
	}

	/**
	 * 当前模板内容
	 * @param string $template
	 */
	private function getContent($template = '') {
		!$template && $template = $this->windView->templateName;
		if ($template)
			echo $this->windFetch($template);
	}

	/**
	 * @return WindView
	 */
	public function getWindView() {
		return $this->windView;
	}

}