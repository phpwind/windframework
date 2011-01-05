<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
class IndexController extends WindController {
	private $a = 2;
	public $b = 3;
	protected $c;
	public function run() {
		$this->setOutput(array('content' => 'hello world'));
		$this->setOutput(array('name' => '【鹊桥】', 'title' => 'SmartyDemo测试', 
								'count'=>'8888888'));
		$this->setTemplate('body');
	}
	
	public function rediect() {
		$this->forwardRedirectAction('showBody', '', array('arg1' => $this->getInput('p'), 'arg2' => '肖肖'));
	}
	
	public function showBody() {
		$this->setOutput(array('arg3' => '我是第二次forward中产生的CTO!'));
		$this->setOutput($this->getInput(array('arg1', 'arg2')));
		$this->setTemplate('forward');
	}
}