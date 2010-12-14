<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class TemplateVarShareController extends WindController {
	public function run() {
		$this->setOutput('测试模板变量域，我是主Action变量', 'bodyArg');
		$this->setTemplate('templateVarShare.index');
	}
	
	public function header() {
		echo 'I am Header<br/>';
		$this->setOutput('我是头部的变量', 'headerArg');
		$this->setTemplate('templateVarShare.head');
	}
	
	public function footer() {
		echo '<hr/>I am footer!!<br/>';
		$this->setOutput('我是尾部的变量', 'footerArg');
		$this->setTemplate('templateVarShare.foot');
	}
}