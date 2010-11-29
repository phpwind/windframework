<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class OtherAction extends WindBaseAction {
	
	public function run() {
		echo "other action";
		$this->setTemplate('header');
	}
}