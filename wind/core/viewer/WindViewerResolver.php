<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.viewer.IWindViewerResolver');
L::import('WIND:core.WindComponentModule');
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
class WindViewerResolver extends WindComponentModule implements IWindViewerResolver {

	/**
	 * 模板信息
	 * @var WindView
	 */
	protected $windView = null;

	/**
	 * 模板类
	 * @var WindViewTemplate
	 */
	protected $windTemplate = null;

	/**
	 * 模板输出的变量
	 * @var array
	 */
	protected $templateVars = array();

	/**
	 * @var WindUrlHelper
	 */
	protected $urlHelper = null;

	/**
	 * 立即输出模板内容
	 * 
	 * @param string $template
	 */
	public function displayWindFetch($template = '') {
		echo $this->windFetch($template);
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windFetch()
	 */
	public function windFetch($template = '') {
		if (!$template && null !== $layout = $this->getWindView()->getLayout()) {
			$template = $layout;
		}
		ob_start();
		$this->render($template);
		return ob_get_clean();
	}

	/**
	 * 设置模板变量信息
	 * 
	 * @param object|array|string $vars
	 * @param string $key
	 */
	public function windAssign($vars, $key = '') {
		$this->templateVars = array();
		if ($key) {
			$this->templateVars[$key] = $vars;
			return;
		}
		if (is_object($vars)) $vars = get_object_vars($vars);
		if (is_array($vars)) $this->templateVars += $vars;
	}

	/**
	 * 编译模板并返回编译后模板名称
	 * @param string $template
	 * @param string $suffix
	 * @param boolean $output
	 * @return string
	 */
	public function compile($template, $suffix = '', $output = false) {
		$templateFile = $this->getWindView()->getViewTemplate($template, $suffix);
		if (!file_exists($templateFile)) {
			throw new WindException($templateFile, WindViewException::VIEW_NOT_EXIST);
		}
		if (!$this->getWindView()->getCompileDir()) return $templateFile;
		$compileFile = $this->getWindView()->getCompileFile($template, 'tpl');
		$_output = $this->getWindTemplate()->compile($templateFile, $compileFile, $this);
		if ($output === false) return $compileFile;
		if ($_output !== null) return array($compileFile, $_output);
		if ($fp = @fopen($compileFile, 'r')) {
			while (!feof($fp))
				$_output .= fgets($fp, 4096);
			fclose($fp);
		} else
			throw new WindViewException('Unable to open the template file \'' . $compileFile . '\'.');
		
		return array($compileFile, $_output);
	}

	/**
	 * 加载视图模板文件
	 * @param template
	 */
	protected function render($template) {
		$_tmp = $this->compile($template);
		//extract template vars
		@extract((array) $this->templateVars, EXTR_REFS);
		if (!include $_tmp) {
			throw new WindViewException($_tmp, WindViewException::VIEW_NOT_EXIST);
		}
	}

	/**
	 * 向上继承，布局切片设置
	 * @param string $segment
	 * @deprecated
	 */
	private function setSegments($segment = '') {
		$this->getContent($segment);
	}

	/**
	 * 当前模板内容
	 * @param string $template
	 */
	private function getContent($template = '') {
		if (!$template) $template = $this->getWindView()->getTemplateName();
		if ($template) $this->displayWindFetch($template);
	}

	/**
	 * @return WindView $windView
	 */
	public function getWindView() {
		if ($this->windView !== null)
			return $this->windView;
		else
			throw new WindException('WindView', WindException::ERROR_RETURN_TYPE_ERROR);
	}

	/**
	 * @return WindViewTemplate $windTemplate
	 */
	public function getWindTemplate() {
		if ($this->windTemplate !== null)
			return $this->windTemplate;
		else
			throw new WindException('getWindTemplate', WindException::ERROR_RETURN_TYPE_ERROR);
	}

	/* (non-PHPdoc)
	 * @see WindModule::getWriteTableForGetterAndSetter()
	 */
	protected function getWriteTableForGetterAndSetter() {
		return array('windView', 'windTemplate', 'urlHelper', 'templateVars');
	}

}