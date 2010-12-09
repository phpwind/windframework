<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('controllers.errorControllers');
class indexController extends WindController {
	public function run() {
		$this->setOutput('欢迎进入Form组件的测试', 'title');
		$this->setTemplate('index');
	}
	public function setHeader() {
		$this->setOutput(array('name'=> '亲爱的朋友'));
		$this->setTemplate('header');
	}
	public function setFooter() {
		$this->setOutput(array('footer'=> array('2010-2110', '@phpwind'),
								'version' => 'WindFrameWorkV1.0'));
		$this->setTemplate('footer');
	}
}