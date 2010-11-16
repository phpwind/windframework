<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WBaseViewer {
	protected $output = '';
	protected $template = '';
	protected $viewContainer = '';
	protected $vars = array();
	
	public function __construct($tpl = '') {
		$this->template = $tpl;
	}
	
	abstract public function display();
	
	abstract public function assign($vars = '', $key = null);
	
	abstract protected function fetch();
}