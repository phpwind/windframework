<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WViewer {
	private $output = '';
	private $template = '';
	private $viewContainer = '';
	private $vars = array();
	
	public function __construct($tpl = '') {
		$this->template = $tpl;
	}
	
	/**
	 * 将变量注册到模板空间中
	 * 
	 */
	public function assign($vars = '', $key = null) {
		if ($key) {
			$this->vars[$key] = $vars;
			return;
		}
		if (is_array($vars)) {
			foreach ($vars as $k => $v) {
				$this->vars[$k] = $v;
			}
		} elseif (is_object($vars)) {
			$this->vars += get_object_vars($vars);
		}
	}
	
	public function display() {
		$this->fetch();
		return $this->viewContainer;
	}
	
	/**
	 * 获取模板内容
	 */
	private function fetch() {
		if (!file_exists($this->template))
			throw new WException('the template file ' . $this->template . ' is not exists.');
		
		if ($this->vars)
			extract($this->vars, EXTR_REFS);
		
		ob_start();
		include $this->template;
		$this->viewContainer = ob_get_clean();
	}

}