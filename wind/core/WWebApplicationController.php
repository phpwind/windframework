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
class WWebApplicationController implements WApplicationController {
	private $router = NULL;
	
	function processRequest($request) {
	}
	
	function initApplicationController() {

	}
	
	/**
	 * 返回一个过滤链
	 * @param WSystemConfig $configObj
	 * @param WRouter $router
	 * @return WFilterChain
	 */
	function createFilterChain($configObj, $router) {
		return new WFilterChain($configObj, $router);
	}
	
	/**
	 * @param WSystemConfig $configObj
	 * @return WUrlRouteParser
	 */
	function createRouterParser($configObj) {
		$parser = $configObj->getRouterConfig('parser');
		$path = $configObj->getRouterParser($parser);
		W::import($configObj->getRouterParser($parser));
		//TODO get router class name
		return new WUrlRouter();
	}

}