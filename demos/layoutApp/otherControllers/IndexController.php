<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class IndexController extends WindController {
	public function run() {
		echo "谢谢您请求我！";
		$this->setOutput('我是在头部被请求的action中的变量！','arg2');
		$this->setTemplate('Layout_run');
	}
}