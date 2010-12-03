<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class OtherController extends WindController {
	
	public function header() {
		$this->setViewData(array('test1'=>'asdfasdfsafd'));
		$this->setTemplate('header');
	}
	
	public function footer(){
		$this->setTemplate('footer');
	}
}