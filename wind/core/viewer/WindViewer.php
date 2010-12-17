<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.viewer.base.IWindViewer');
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
	
	protected $templateName = '';
	protected $templatePath = '';
	protected $templateExt = '';
	
	protected $view = null;
	protected $layout = null;
	
	protected $var = array();
	
	/**
	 * 获取模板信息
	 */
	public function windFetch($template = '') {
		$varName = $this->getTemplateVarName();
		@extract($this->var[$varName], EXTR_REFS);
		ob_start();
		if (($segments = $this->parserLayout()) == null) {
			$template = $this->getViewTemplate($template);
			if ($template) include $template;
		} else {
			foreach ($segments as $value) {
				$template = $this->getViewTemplate($value);
				if (is_file($template)) @include $template;
			}
		}
		return ob_get_clean();
	}
	
	/**
	 * 理解输出模板内容
	 * 
	 * @param string $template
	 */
	public function immediatelyWindFetch($template = '') {
		echo $this->windFetch($template);
	}
	
	/**
	 * 以对象方式访问模板变量
	 * @param string $templateName
	 */
	public function getVarWithObject($templateName = '') {
		$varName = $templateName . 'Object';
		if (!isset($this->$varName)) {
			$this->$varName = new stdClass();
			foreach ($this->$templateName as $key => $value) {
				$this->$varName->$key = $value;
			}
		}
		return $this->$varName;
	}
	
	/**
	 * 以数组方式获得模板变量
	 * @param string $key
	 * @param string $templateName
	 * @return Ambigous <unknown, string>
	 */
	public function getVar($key = '', $templateName = '') {
		if ($templateName === '') $templateName = $this->getTemplateVarName();
		return $key === '' ? $this->var[$templateName] : $this->var[$templateName][$key];
	}
	
	/**
	 * @param string $actionHandle
	 */
	public function doAction($actionHandle = '', $path = '') {
		if ($this->view === null) throw new WindException('excute doAction method failed.');
		$this->view->doAction($actionHandle, $path);
	}
	
	/**
	 * 设置模板变量信息
	 * 
	 * @param object|array|string $vars
	 * @param string $key
	 */
	public function windAssign($vars, $key = '') {
		$varName = $this->getTemplateVarName();
		$this->var[$varName] = array();
		if ($key) {
			$this->var[$varName][$key] = $vars;
			return;
		}
		if (is_object($vars)) $vars = get_object_vars($vars);
		if (is_array($vars)) $this->var[$varName] += $vars;
	}
	
	/**
	 * 设置视图信息
	 * 
	 * @param WindView $view
	 */
	public function initWithView($view) {
		$this->templateName = $view->templateDefault;
		$this->templatePath = $view->templateDir;
		$this->templateExt = $view->templateExt;
		$this->layout = $view->layout;
		$this->view = $view;
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
	 * 模板路径解析
	 * 根据模板的逻辑名称，返回模板的绝对路径信息
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	private function getViewTemplate($templateName = '', $templateExt = '') {
		if (!$templateName) $templateName = $this->templateName;
		if (!$templateExt) $templateExt = $this->templateExt;
		if (strrpos($templateName, ':') === false) {
			$templateName = $this->templatePath . '.' . $templateName;
		}
		return L::getRealPath($templateName) . '.' . $templateExt;
	}
}