<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('controllers.errorControllers');
class IndexController extends WindController {
	public function run() {
		$this->setOutput('欢迎进入Form组件的测试', 'title');
		$this->setTemplate('index');
	}
	public function setHeaderAction() {
		$this->setOutput(array('name'=> '亲爱的朋友'));
		$demos = array('../helloworld' => 'HelloWorld', 
						'index.php?m=other&c=Index&a=iforward' => 'action中可带参数跳转应用',
						'index.php?m=other&c=Index&a=getInfo' => 'action中请求另外的action操作',
						'index.php?m=other&c=TemplateVarShare' => '模板变量的共享',
						'../smartyapp' => 'Smarty模板引擎接入',
						'../dbapp' => '数据库demo',
						'../layoutApp' => 'LayOut使用demo',
						'../multiTemplateApp' => '多模板应用demo', 
		 				'../ajax' => 'Ajax应用Demo');
		$this->setOutput(array('demos' => $demos));
		$this->setTemplate('header');
	}
	public function setFooterAction() {
		$this->setOutput(array('footer'=> array('2010-2110', 'phpwind'),
								'version' => 'WindFrameWorkV1.0'));
		$this->setOutput($this->getCompaticyInfo());
		$this->setTemplate('footer');
	}
	
	private function getCompaticyInfo() {
		$fileNum = count(get_included_files());
		list($start_usec, $start_sec) = explode(' ', $_SESSION['start']);
         /* 内存占用情况 */
		(function_exists('memory_get_usage')) && $memory = (memory_get_usage() / 1048576);
		list($now_usec, $now_sec) = explode(' ', microtime());
		$excustTime = number_format(($now_sec - $start_sec) + ($now_usec - $start_usec), 6);
		return array('includeFileNum' => $fileNum, 'excuceTime' => $excustTime, 'memory' => $memory);
	}
}