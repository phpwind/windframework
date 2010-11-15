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
abstract class WActionController {
	protected $request = null;
	protected $response = null;
	
	protected $forward = '';
	protected $viewer = null;
	
	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
		$this->viewer = new stdClass();
	}
	
	public function run() {

	}
	
	/**
	 * ·µ»ØÊÓÍ¼¶ÔÏñ
	 * 
	 * @param WSystemConfig $configObj
	 * @param WRouter $router
	 * @return stdClass
	 */
	public function getViewer($configObj, $router) {
		if ($this->forward == '')
			$this->forward = $router->getDefaultViewHandle();
			
		$viewer = WViewFactory::getInstance($configObj)->create($this->forward);
		$viewer->assign($this->viewer);
		return $viewer;
	}

}