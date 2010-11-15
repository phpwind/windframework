<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WAction {
	private $request = null;
	private $response = null;
	
	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}
	
	static function run() {
	}
	
}