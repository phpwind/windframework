<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class ForwardController extends WindController {
	public function run() {
		$this->setOutput(array('arg2' => '我是第一次forward中产生的CTO!'));
		$tmp = array('arg2' => '我是第一次forward中产生的CTO!',
					'arg1' => $this->getInput('p'));
		$this->forwardRedirectAction('showBody', '', $tmp);
	}
	
	public function showBody() {
		$this->setOutput(array('title' => 'forward测试',
								'arg3' => '我是第二次forward中产生的CTO!'));
		$this->setOutput($this->getInput(array('arg1', 'arg2')));
		$this->setTemplate('forward');
	}
	
	public function req() {
		echo '我在测试forwardAction<br/>';
		
	}
}