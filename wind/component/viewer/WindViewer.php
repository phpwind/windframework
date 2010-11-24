<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::register('viewer', dirname(__FILE__));
L::import('viewer:base.impl.WindViewerImpl');
/**
 * 默认视图引擎
 * 基于URL的视图引擎，视图名和模板名称保持一致
 * 
 * 该视图类接收一个modelAndView对象，通过解析该对象获得一个逻辑视图名称
 * 并将该逻辑视图名称，映射到具体的视图资源。
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindViewer implements WindViewerImpl {
	
	protected $template = '';
	protected $templatePath = '';
	protected $view = null;
	
	/**
	 * 视图变量信息
	 * @var $vars
	 */
	protected $vars = array();
	
	/**
	 * 获取模板信息
	 */
	public function windFetch($template = '') {
		$template = $this->getViewTemplate($template);
		if ($this->vars) extract($this->vars, EXTR_REFS);
		
		ob_start();
		@include $template;
		
		return ob_get_clean();
	}
	
	/**
	 * 设置模板变量信息
	 * 
	 * @param object|array|string $vars
	 * @param string $key
	 */
	public function windAssign($vars, $key = '') {
		if (is_array($vars))
			$this->windAssignWithArray($vars, $key);
		elseif (is_object($vars))
			$this->windAssignWithObject($vars, $key);
		else
			$this->windAssignWithSimple($vars, $key);
	}
	
	/**
	 * 设置模板变量
	 * 
	 * @param $vars
	 * @param string $key
	 */
	public function windAssignWithSimple($vars, $key = '') {
		if ($key) $this->vars[$key] = $vars;
	}
	
	/**
	 * 设置模板变量
	 * 
	 * @param object $vars
	 * @param string $key
	 */
	public function windAssignWithObject($vars, $key = '') {
		if (!is_object($vars)) return;
		if ($key) $this->vars[$key] = $vars;
		$this->vars += get_object_vars($vars);
	}
	
	/**
	 * 设置模板变量
	 * 
	 * @param array $vars
	 * @param string $key
	 */
	public function windAssignWithArray($vars, $key = '') {
		if (!is_array($vars)) return;
		if ($key) $this->vars[$key] = $vars;
		foreach ($vars as $key => $value) {
			$this->vars[$key] = $value;
		}
	}
	
	/**
	 * 获得模板文件
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return array()
	 */
	public function getViewTemplate($templateName = '', $templateExt = '') {
		if (!$templateName) $templateName = $this->view->getTemplateName();
		if (!$templateExt) $templateExt = $this->view->getTemplateExt();
		$templatePath = $this->templatePath;
		$templatePath = $this->_getViewTemplate($templateName, $templatePath, $templateExt);
		return $templatePath;
	}
	
	/**
	 * 根据模板名称获得模板文件
	 * 
	 * @param string $viewName
	 * @return array()
	 */
	private function _getViewTemplate($templateName, $templatePath, $templateExt = '') {
		if (!$templateName) throw new WindException('template file is not exists.');
		$filePath = $templatePath . '.' . $templateName;
		return L::getRealPath($filePath, false, $templateExt);
	}
	
	/**
	 * 设置视图信息
	 * 
	 * @param WindView $view
	 */
	public function initViewerResolverWithView($view) {
		$this->template = $view->getTemplateName();
		$this->templatePath = $view->getTemplatePath();
		$this->view = $view;
	}
}