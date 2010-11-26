<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once ('Smarty.class.php');

class WindSmarty extends Smarty implements WindViewerImpl {
	protected $template = '';
	protected $templatePaty = '';
	
	protected $view = null;
	
	public function __construct($tpl = '') {
		$this->tpl = $tpl;
		$this->left_delimiter = '<!--{';
		$this->right_delimiter = '}-->';
	}
	
	public function setTpl($tpl = '') {
		if ($tpl) $this->tpl = $tpl;
	}
	
	public function setLayout($layout = '') {
		if (!$layout) return;
		$layout->setTpl($this->tpl);
		$this->_layout = $layout;
	}
	
	public function setCacheDir($cacheDir) {
		if ($cacheDir)
			$this->cache_dir = realpath($cacheDir . '/');
		else
			$this->cache_dir = 'cache/';
	}
	public function setCompileDir($compileDir) {
		if ($compileDir)
			$this->compile_dir = realpath($compileDir . '/');
		else
			$this->compile_dir = 'compile/';
	}
	public function setTemplateDir($templateDir) {
		if ($templateDir)
			$this->template_dir = realpath($templateDir . '/');
		else
			$this->template_dir = 'templates/';
	}
	
	public function windAssign($var = '', $value = null) {
		if (is_object($value)) {
			$this->assignByRef($var, $value);
		} else {
			(is_object($var)) ? $this->assign(get_object_vars($var)) : $this->assign($var, $value);
		}
	}
	
	public function windFetch() {
		return $this->fetch($this->template, null, null, null, false);
	}
	
	/**
	 * @param WindView $view
	 */
	public function initViewerResolverWithView($view) {
		$this->templatePath = $view->getTemplatePath();
		$this->template = $this->getViewTemplate($view->getTemplateName(), 'phtml');
		$this->view = $view;
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

}