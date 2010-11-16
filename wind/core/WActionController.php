<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WActionController extends WBaseAction {
	
	public function __construct($request, $response) {
		parent::__construct();
		$this->request = $request;
		$this->response = $response;
	}
	
	public function run() {}
}