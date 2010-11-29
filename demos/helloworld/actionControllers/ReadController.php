<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class ReadController extends WindBaseAction {
	
	public function run() {
		$this->getModelAndView()->setActionPath('otherControllers.OtherAction');
		$this->getModelAndView()->setAction('run');
		$this->setTemplate('read');
	}
}