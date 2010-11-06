<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
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
 * the last known user to change this file in the repository  
 * <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WBaseFrontController {
	
	/**
	 * 请求处理的核心处理器
	 * 通过实现该方法，完成请求的核心处理工作
	 * 
	 * @param WHttpRequestContext $request
	 */
	abstract protected function processRequest($request);
	
	/**
	 * 请求转发，将控制权从应用系统的一部分转交给另一个部分
	 * 此处通过实现dispatch方法，将请求处理机制转交给视图处理组件
	 * @param WHttpRequestContext $request
	 * @param $viewModel
	 */
	abstract protected function dispatch($request);
	
	function __construct() {
		$this->_initConfig();
		$this->_initRequest();
	}
	
	/**
	 * 接受客户请求并完成请求的初始化工作
	 * 在这个操作中将分析用户的请求头部信息，
	 * 并根据请求头部信息创建请求对象
	 * 
	 * @param WRequestContext $request
	 */
	function run() {
		$request = Null;
		$this->service($request);
	}
	
	/**
	 * 初始化配置文件
	 */
	private function _initConfig() {

	}
	
	/**
	 * 初始化请求
	 */
	private function _initRequest() {

	}
	
	/**
	 * 初始化响应
	 */
	private function _initResponse() {

	}
	
	/**
	 * 处理客户的请求
	 * 该方法根据客户端的请求类型，调用相应的方法进行处理
	 * @param WHttpRequestContext $request
	 * @access private
	 */
	private function service($request) {
		$this->doGet($request);
	}
	
	/**
	 * 处理HTTP请求的GET方法
	 * @param WHttpRequestContext $request
	 * 
	 */
	protected function doGet($request) {
		$this->processRequest($request);
	}
	
	/**
	 * 处理HTTP请求的POST方法
	 * @param WHttpRequestContext $request
	 */
	protected function doPost($request) {
		$this->processRequest($request);
	}
	
}