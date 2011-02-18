<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class OtherController extends WindController {
	
	public function header() {
//		$this->setOutput(array('test1'=>'header vers.'));
//		$this->setTemplate('header');
		echo 'aaaa';
	}
	
	public function footer(){
		$this->setTemplate('footer');
	}
}