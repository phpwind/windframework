<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class indexController extends WindController {
	public function run() {
		//$this->mav->setAction('getHeader');
		$this->setViewData('哈哈，我是一个全页面的变量！所以你在这里看到我了', 'global');
		$this->setTemplate('index');
	}
	public function layout() {
		$this->setViewData(array('name'=> 'layout'));
		$this->setViewData('哈哈，我是一个全页面的变量！所以你在这里看到我了', 'global');
		L::import("WIND:component.viewer.WindLayout");
		$layout = new WindLayout();
		$layout->setLayoutFile('layout');
		$this->setLayOut($layout);
	}
	public function showForm() {
		$this->setViewData(array('isUse' => $this->getParaments('none')));
		$this->setTemplate('userForm');
	}
	public function getForm() {
		if (!$this->getParaments('formName')) {
			L::import('controllers.actionForm.UserForm');
			$userInfo = L::getInstance('UserForm');
			var_dump($this->getParaments(array('username', 'password')));
			$userInfo->setProperties($this->getParaments(array('username', 'password')));
			$this->setViewData(array('notice' => '你没有使用userForm'));
		} else {
			$userInfo = L::getInstance('UserForm');
		}
		$this->setViewData($userInfo, 'userInfo');
		$this->setTemplate('userForm');
	}
	
	public function getHeader() {
		$this->setViewData(array('name'=> '亲爱的朋友'));
		$this->setTemplate('header');
	}
	public function getFooter() {
		$this->setViewData(array('footer'=> array('2010-2110', '@phpwind'),
								'version' => 'WindFrameWorkV1.0'));
		$this->setTemplate('footer');
	}
}