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
class WindViewerResolver extends WindModule implements IWindViewerResolver {
	/**
	 * @var WindView
	 */
	protected $windView = null;

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windFetch()
	 */
	public function windFetch($template = '') {
		if (!$template && null !== ($layout = $this->getWindView()->getLayout())) {
			$template = $layout;
		}
		ob_start();
		$this->render($template);
		return ob_get_clean();
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windAssign()
	 */
	public function windAssign($vars, $key = '') {
		if ($key === '') $key = $this->getWindView()->getTemplateName();
		$this->getResponse()->setData($vars, $key);
	}

	/**
	 * 立即输出模板内容
	 * 
	 * @param string $template
	 * @param WindView $view
	 */
	public function displayWindFetch($template = '') {
		echo $this->windFetch($template);
	}

	/**
	 * 编译模板并返回编译后模板名称
	 * 
	 * @param string $template
	 * @param string $suffix
	 * @param boolean $output
	 * @return string
	 */
	public function compile($template, $suffix = '', $output = false) {
		$templateFile = $this->getWindView()->getViewTemplate($template, $suffix);
		if (!is_file($templateFile)) {
			throw new WindViewException('[component.viewer.WindView.parseFilePath] ' . $templateFile, 
				WindViewException::VIEW_NOT_EXIST);
		}
		$compileFile = $this->getWindView()->getCompileFile($template, 'php');
		
		/* @var $_windTemplate WindViewTemplate */
		$_windTemplate = $this->getSystemFactory()->getInstance(COMPONENT_TEMPLATE);
		$_output = $_windTemplate->compile($templateFile, $this);
		
		if (!$compileFile && !$_output) return array('', '');
		WindFile::write($compileFile, $_output);
		return array($compileFile, $_output);
	}

	/**
	 * 加载视图模板文件
	 * 
	 * @param template
	 */
	protected function render($template) {
		list($_tmp, $_output) = $this->compile($template);
		@extract((array) $this->getResponse()->getData($this->getWindView()->getTemplateName()), EXTR_REFS);
		if (!include $_tmp) {
			throw new WindViewException(
				'[component.viewer.ViewerResolver.render] template:' . $template . ' compile template:' . $_tmp, 
				WindViewException::VIEW_NOT_EXIST);
		}
	}

	/**
	 * 检查是否需要重新编译
	 * 
	 * @param string $templateFile
	 * @param string $compileFile
	 */
	private function checkReCompile($templateFile, $compileFile) {
		$_reCompile = false;
		if (IS_DEBUG) {
			$_reCompile = true;
		} elseif (false === ($compileFileModifyTime = @filemtime($compileFile))) {
			$_reCompile = true;
		} else {
			$templateFileModifyTime = @filemtime($templateFile);
			if ((int) $templateFileModifyTime >= $compileFileModifyTime) $_reCompile = true;
		}
		return $_reCompile;
	}

	/**
	 * 当前模板内容
	 * 
	 * @param string $template
	 */
	private function getContent($template = '') {
		if (!$template) $template = $this->getWindView()->getTemplateName();
		if ($template) $this->displayWindFetch($template);
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

}