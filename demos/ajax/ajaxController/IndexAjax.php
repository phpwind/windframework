<?php 
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-24
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class IndexAjax extends WindAction {
	public function run() {
		$this->setTemplate('index');
	}
	public function getJson() {
		$info = array('您好!', '您正在测试ajax的调用');
		echo json_encode($info);
		$this->setTemplate('default');
	}
	public function getTimeByInput() {
		date_default_timezone_set('Asia/ShangHai');
		echo date('Y-m-d H:i:s', time());
		$this->setTemplate('default');
	}
}
?>