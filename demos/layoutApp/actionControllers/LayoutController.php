<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.viewer.WindLayout");
class LayoutController extends WindController {
	public function run() {
		$this->setOutput('您好，欢迎您的光临！','arg');
		$layout = new WindLayout();
		$layout->setLayoutFile('index');
		$this->setLayout($layout);
		$this->setTemplate('content');
	}
}