<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindBaseAction');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindController extends WindBaseAction {
	
	/**
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct(WindHttpRequest $request, WindHttpResponse $response) {
		parent::__construct();
		$this->request = $request;
		$this->response = $response;
		$this->setDefaultViewHandle();
	}
	
	public function run() {}
	
	private function setDefaultViewHandle() {
		$view = $this->response->getRouter()->getController() . '_' . $this->response->getRouter()->getAction();
		$this->setDefaultViewTemplate($view);
	}

}