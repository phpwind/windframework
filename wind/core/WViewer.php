<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WViewer implements WBaseViewer {
	private $_layout = null;
	
	/**
	 * 视图模板的路径信息
	 * 
	 * @var $template
	 */
	protected $tpl = '';
	
	/**
	 * 视图内容
	 * @var $viewContainer
	 */
	protected $viewContainer = '';
	
	/**
	 * 视图变量信息
	 * 
	 * @var $vars
	 */
	protected $vars = array();
	
	/**
	 * 设置layout对象
	 * 
	 * @param WLayout $layout
	 * @return
	 */
	public function setLayout($layout = '') {
		if (!$layout)
			return;
		$layout->setTpl($this->tpl);
		$this->_layout = $layout;
	}
	
	public function windDisplay($tpl = '') {
		if ($tpl)
			$this->tpl = $tpl;
		
		$this->windFetch();
		return $this->viewContainer;
	}
	
	public function windAssign($vars = '', $key = null) {
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
	
	public function windFetch() {
		if (!file_exists($this->tpl))
			throw new WException('the template file ' . $this->tpl . ' is not exists.');
		
		if ($this->vars)
			extract($this->vars, EXTR_REFS);
		
		ob_start();
		if ($this->_layout) {
			$this->_layout->parser();
			$segments = $this->_layout->getSegments();
			foreach ($segments as $key => $value) {
				@include $value;
			}
		} else {
			@include $this->tpl;
		}
		$this->viewContainer = ob_get_clean();
	}
	
	/**
	 * @param string $tpl
	 */
	public function setTpl($tpl = '') {
		$this->tpl = $tpl;
	}
	
	public function setCacheDir($cacheDir) {
		//TODO if has compile then do same about config here
	}
	public function setCompileDir($compileDir) {
		//TODO if has compile then do same about config here
	}
}