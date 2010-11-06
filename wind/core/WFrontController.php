<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */


/**
 * 
 * 抽象的前端控制器接口，通过集成该接口可以实现以下职责
 * 
 * 职责定义：
 * 接受客户请求
 * 处理请求
 * 向客户端发送响应
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WFrontController extends WBaseFrontController {
	
	/**
	 * @param unknown_type $request
	 */
	protected function processRequest($request) {
		WRouterManager::init();
		echo "hello world";
	}
	
	/**
	 * @param unknown_type $request
	 */
	protected function dispatch($request) {

	}

}