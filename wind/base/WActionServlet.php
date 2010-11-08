<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
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
abstract class WActionServlet {
	protected $reuqest = null;
	
	function __construct() {
		$this->init();
	}
	
	function init() {
		$this->_initRequest();
	}
	
	function run() {
		if ($this->reuqest === null)
			throw new WException('init action servlet failed!!');
		$this->service($this->reuqest);
	}
	
	/**
	 * @return WRequest
	 */
	private function _initRequest() {
		$reuqest = W::getInstance('WHttpRequest');
		$this->reuqest = $reuqest;
	}
	
	abstract protected function process();
	
	/**
	 * @param WHttpRequest $request
	 */
	protected function service($request) {
		$requestMethod = $request->getRequestMethod();
		if ($requestMethod == 'GET')
			$this->doGet($request);
		else if ($requestMethod == 'POST')
			$this->doPost($request);
		else if ($requestMethod == 'PUT')
			$this->doPut($request);
		else if ($requestMethod == 'DELETE')
			$this->doDelete($request);
		else
			throw new Exception('your request method is not supported!!!');
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doPost($request) {
		$this->process();
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doGet($request) {
		$this->process();
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doPut($request) {
		$this->process();
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doDelete($request) {
		$this->process();
	}

}