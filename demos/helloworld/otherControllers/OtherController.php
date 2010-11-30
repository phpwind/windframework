<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class OtherController extends WindBaseAction {
	
	public function run() {
		$this->setViewData(array('test1'=>'asdfasdfsafd'));
		$this->setTemplate('read');
	}
}