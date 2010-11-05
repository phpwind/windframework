<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

WBasic::import('router.WRouterContext');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WUrlRouteParser implements WRouteParser {
	
	private $_urlRule;
	
	private $routerContext;
	
	public function __construct() {
		$this->routerContext = WRouterContext::getInstance();
	}
	
	/**
	 * 调用该方法实现路由解析
	 * 获得到 request 的静态对象，得到request的URL信息
	 * 获得 config 的静态对象，得到URL的格式信息
	 * 解析URL，并声称RouterContext对象
	 * @return WRouterContext
	 */
	public function doParser() {
		$this->_init();
		
		return $this->routerContext;
	}
	
	/**
	 * 路由解析器初始化操作
	 */
	private function _init() {
		$config = array();
		$routeChina = $this->_parserUrl($config);
//		$this->routerContext->
	}
	
	/**
	 * 根据配置规则解析Url, 并返回解析结果集
	 * 
	 * @param array $config
	 * @return array('操作','应用','应用集合1','应用集合2');
	 */
	private function _parserUrl($config) {
		
		return array('a','c');
	}

}