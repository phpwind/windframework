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
		
		//设置默认的视图名称
		$default = $response->getRouter()->getDefaultViewHandle();
		$this->setDefaultViewTemplate($default);
	}
	
	public function run() {}

}