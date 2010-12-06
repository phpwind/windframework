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
		//$this->mav->setAction('getHeader');
		$this->setOutput('哈哈，我是一个全页面的变量！所以你在这里看到我了', 'global');
		$this->setTemplate('index');
	}
	public function layout() {
		//TODO
	}
	public function showForm() {
		$this->setOutput('show', 'show');
		$this->setOutput(array('isUse' => $this->getInput('none')));
		$this->setTemplate('userForm');
	}
	public function getForm() {
		if (!$this->getInput('formName')) {
			L::import('controllers.actionForm.UserForm');
			$userInfo = L::getInstance('UserForm');
			$userInfo->setProperties($this->getInput(array('username', 'password')));
			$this->setOutput(array('notice' => '你没有使用userForm'));
		} else {
			$userInfo = L::getInstance('UserForm');
		}
		$this->setOutput($userInfo, 'userInfo');
		$this->setTemplate('userForm');
	}
	
	public function getHeader() {
		$this->setOutput(array('name'=> '亲爱的朋友'));
		$this->setTemplate('header');
	}
	public function getFooter() {
		$this->setOutput(array('footer'=> array('2010-2110', '@phpwind'),
								'version' => 'WindFrameWorkV1.0'));
		$this->setTemplate('footer');
	}
}