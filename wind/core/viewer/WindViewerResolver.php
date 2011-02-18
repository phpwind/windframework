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
	 *
	 * @var WindView
	 */
	protected $windView = null;

	/**
	 * 模板类
	 *
	 * @var WindViewTemplate
	 */
	protected $windTemplate = null;

	/**
	 * 模板输出的变量
	 *
	 * @var array
	 */
	protected $templateVars = array();

	/**
	 * 模板布局对象
	 *
	 * @var WindLayout
	 */
	protected $layout = null;

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
		ob_start();
		if (($segments = $this->parserLayout()) == null) {
			$this->render($template);
		} else {
			foreach ($segments as $value) {
				$this->render($template);
			}
		}
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
	 * 加载视图模板文件
	 * @param template
	 */
	protected function render($template) {
		//extract template vars
		@extract((array) $this->templateVars, EXTR_REFS);
		
		$templateFile = $this->getWindView()->getViewTemplate($template);
		$compileFile = $this->getWindView()->getCompileFile($template);
		$this->getWindTemplate()->render($templateFile, $compileFile, $this->getWindView());
		
		if (!include $compileFile) {
			throw new WindViewException($template, WindViewException::VIEW_NOT_EXIST);
		}
	}

	/**
	 * 获得模板变量名称
	 */
	private function getTemplateVarName() {
		$varName = $this->templateName ? $this->templateName : 'default';
		return $varName;
	}

	/**
	 * 如果存在布局文件则解析布局信息
	 * @return array()
	 */
	private function parserLayout() {
		if ($this->layout === null) return null;
		return $this->layout->parserLayout($this->templatePath, $this->templateExt, $this->templateName);
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
		return array('windView', 'windTemplate');
	}

}