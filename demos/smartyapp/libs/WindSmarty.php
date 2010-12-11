<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once ('Smarty.class.php');
L::import('WIND:component.viewer.base.IWindViewer');
class WindSmarty extends Smarty implements IWindViewer {
	protected $template = '';
	protected $view = null;
	protected $layout;
	protected $templateExt;
	
	public function __construct() {
		$this->left_delimiter = '<!--{';
		$this->right_delimiter = '}-->';
	}

	/**
	 * 
	 * @param WindView $view
	 */
	public function initWithView($view) {
		$this->template = $view->getTemplateName();
		$this->template_dir = $this->getRealPath($view->getTemplateDir());
		$this->templateExt = $view->getTemplateExt();
		$this->cache_dir = $this->getRealPath($view->getTemplateCacheDir());
		$this->compile_dir = $this->getRealPath($view->getTemplateCompileDir());
		$this->layout = $view->getForward()->getLayout();
		$this->view = $view;
	}
	
	public function setTpl($tpl = '') {
		if ($tpl) $this->tpl = $tpl;
	}
	
	public function windAssign($vars, $key = '') {
		if (is_array($vars))
			$this->assign($vars, $key);
		elseif (is_object($vars) && is_string($key) && $key != '') {
			$this->assignByRef($key, $vars);
		}
	}
	
	public function windFetch($templateName = '') {
		if ($templateName != '') $templateName = $templateName . '.' . $this->templateExt;
		else $templateName = $this->template . '.' . $this->templateExt;
		return $this->fetch($templateName, null, null, null, false);
	}
	
	/**
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return array()
	 */
	public function getRealPath($path) {
		return L::getRealPath($path . '.*');
	}

	/**
	 * @param string $actionHandle
	 */
	public function doAction($actionHandle = '', $path = '') {
		if ($this->view instanceof WindView) {
			$this->view->doAction($actionHandle, $path);
		}
	}
}