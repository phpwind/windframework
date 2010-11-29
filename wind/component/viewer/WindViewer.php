<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::register('viewer', dirname(__FILE__));
L::import('viewer:base.IWindViewer');
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
class WindViewer implements IWindViewer {
	
	protected $template = '';
	protected $templatePath = '';
	protected $templateExt = '';
	
	protected $view = null;
	protected $layout = null;
	protected $layoutMapping = array();
	
	/**
	 * 视图变量信息
	 * @var $vars
	 */
	protected $vars = array();
	
	/**
	 * 获取模板信息
	 */
	public function windFetch($template = '') {
		if ($this->vars) extract($this->vars, EXTR_REFS);
		ob_start();
		if (($segments = $this->parserLayout()) == null) {
			$template = $this->getViewTemplate($template);
			if ($template) include $template;
		} else {
			foreach ($segments as $value) {
				if (isset($this->layoutMapping[$value])) {
					$value = $this->layoutMapping[$value];
				}
				$template = $this->getViewTemplate($value);
				if (is_file($template)) @include $template;
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
	 * 如果存在布局文件则解析布局信息
	 * @return array()
	 */
	public function parserLayout() {
		if ($this->layout === null) return null;
		return $this->layout->parserLayout($this->templatePath, $this->templateExt);
	}
	
	/**
	 * 模板路径解析
	 * 根据模板的逻辑名称，返回模板的绝对路径信息
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	public function getViewTemplate($templateName = '', $templateExt = '') {
		if (!$templateName) $templateName = $this->template;
		if (!$templateExt) $templateExt = $this->templateExt;
		if (strrpos($templateName, ':') === false) {
			$templateName = $this->templatePath . '.' . $templateName;
		}
		return L::getRealPath($templateName, false, $templateExt);
	}
	
	/**
	 * 设置视图信息
	 * 
	 * @param WindView $view
	 */
	public function initWithView($view) {
		$this->template = $view->getTemplateName();
		$this->templatePath = $view->getTemplatePath();
		$this->templateExt = $view->getTemplateExt();
		$this->layout = $view->getMav()->getLayout();
		$this->layoutMapping = $view->getMav()->getLayoutMapping();
		$this->view = $view;
	}
	
	/**
	 * @return WindView
	 */
	public function getView() {
		return $this->view;
	}
	
	/**
	 * @param string $actionHandle
	 */
	public function doAction($actionHandle = '') {
		if ($this->view instanceof WindView) $this->getView()->doAction($actionHandle);
	}

}