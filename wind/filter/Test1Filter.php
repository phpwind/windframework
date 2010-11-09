<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class Test1Filter extends WFilter {
	
	/**
	 * @param unknown_type $request
	 * @param unknown_type $response
	 */
	public function doBeforeProcess($request, $response) {
		echo __CLASS__ . ' do before <br>';
	}
	
	/**
	 * @param unknown_type $request
	 * @param unknown_type $response
	 */
	public function doAfterProcess($request, $response) {
		echo __CLASS__ . ' do after <br>';
	}

}