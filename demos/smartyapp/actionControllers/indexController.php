<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
class IndexController extends WindController {
	private $a = 2;
	public $b = 3;
	protected $c;
	public function run() {
		$this->setOutput(array('content' => 'hello world'));
		$this->setOutput(array('name' => '¡¾ÈµÇÅ¡¿', 'title' => 'SmartyDemo²âÊÔ', 
								'count'=>'8888888'));
		$this->setTemplate('body');
	}
}