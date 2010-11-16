<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WViewer extends WBaseViewer {
	private $_layout = null;
	
	/**
	 * 显示输出视图
	 * @return string
	 */
	public function display($tpl = '') {
		if ($tpl)
			$this->tpl = $tpl;
		
		$this->fetch();
		return $this->viewContainer;
	}
	
	/**
	 * 将变量注册到模板空间中
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
	
	/**
	 * 设置layout对象
	 * 
	 * @param WLayout $layout
	 * @return
	 */
	public function setLayout($layout) {
		if (!$layout)
			return;
		$layout->setTpl($this->tpl);
		$this->_layout = $layout;
	}
	
	/**
	 * 获取模板内容
	 */
	protected function fetch() {
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

}