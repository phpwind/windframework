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
	protected $response = null;
	
	function __construct() {
		$this->init();
	}
	
	public function init() {
		$this->_initRequest();
	}
	
	/**
	 * @return WRequest
	 */
	private function _initRequest() {
		//TODO ÖØ¹¹
		$reuqest = W::getInstance('WHttpRequest');
		
		$this->reuqest = $reuqest;
	}
	
	public function run() {
		if ($this->reuqest === null)
			throw new WException('init action servlet failed!!');
		$this->service($this->reuqest, $this->response);
	}
	
	abstract function process($request, $resopnse);
	
	/**
	 * @param WHttpRequest $request
	 */
	protected function service($request, $resopnse) {
		$requestMethod = $request->getRequestMethod();
		if ($requestMethod == 'GET')
			$this->doGet($request, $resopnse);
		else if ($requestMethod == 'POST')
			$this->doPost($request, $resopnse);
		else if ($requestMethod == 'PUT')
			$this->doPut($request, $resopnse);
		else if ($requestMethod == 'DELETE')
			$this->doDelete($request, $resopnse);
		else
			throw new Exception('your request method is not supported!!!');
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doPost($request, $resopnse) {
		$this->process($request, $resopnse);
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doGet($request, $resopnse) {
		$this->process($request, $resopnse);
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doPut($request, $resopnse) {
		$this->process($request, $resopnse);
	}
	
	/**
	 * @param WRequest $request
	 */
	protected function doDelete($request, $resopnse) {
		$this->process($request, $resopnse);
	}

}