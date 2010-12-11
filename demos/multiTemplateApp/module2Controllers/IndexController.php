<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */


class IndexController extends WindController {
	public function beforeAction() {
		$this->setTemplateConfig('modle2');
	}
	public function run() {
		echo '我在模板类型2的请求！';
		$this->setOutput(array('test' => '我是模板2中的变量'));
		$this->setTemplate('modle2');
	}
}