<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('Smarty.class.php');

class WSmarty extends Smarty implements WBaseViewer{
	private $_layout = null;
	/**
	 * 视图模板的路径信息
	 * 
	 * @var $template
	 */
	private $tpl = '';
	
	/**
	 * 视图内容
	 * @var $viewContainer
	 */
	private $viewContainer = '';
	
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
		if ($cacheDir) $this->cache_dir = realpath($cacheDir . '/');
		else $this->cache_dir = 'cache/';
	}
	public function setCompileDir($compileDir) {
		if ($compileDir) $this->compile_dir = realpath($compileDir . '/');
		else $this->compile_dir = 'compile/';
	}
	public function setTemplateDir($templateDir) {
		if ($templateDir) $this->template_dir = realpath($templateDir . '/');
		else $this->template_dir = 'templates/';
	}
	public function windDisplay($tpl = '') {
		if ($tpl) $this->tpl = $tpl;
		//$this->display($this->tpl);
		return $this->windFetch();
	}
	
	public function windAssign($var = '', $value = null) {
		if (is_object($value)) {
			$this->assignByRef($var, $value);
		} else {
			(is_object($var)) ? $this->assign(get_object_vars($var)) : $this->assign($var, $value);
		}
	}
	
	public function windFetch(){
		$this->viewContainer = $this->fetch($this->tpl, null, null, null, false);
		return $this->viewContainer;
	}
}