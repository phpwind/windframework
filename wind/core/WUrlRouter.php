<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WUrlRouter extends WRouter {
	protected $_parserName = 'url';
	
	public function __construct() {}
	
	/**
	 * 调用该方法实现路由解析
	 * 获得到 request 的静态对象，得到request的URL信息
	 * 获得 config 的静态对象，得到URL的格式信息
	 * 解析URL，并声称RouterContext对象
	 * @param WSystemConfig $configObj
	 * @return WRouterContext
	 */
	public function doParser($configObj, $request) {
		$this->_urlRule = $configObj->getRouterRule($this->_parserName);
		$this->_init($request);
	}
	
	/**
	 * 路由解析器初始化操作
	 * @param WHttpRequest $request
	 */
	protected function _init($request) {
		if (!$this->_urlRule)
			throw new Exception('url parser rule is empty, please check your config');
		$keys = array_keys($this->_urlRule);
		$this->_action = $request->getGet($keys[0]);
		$this->_controller = $request->getGet($keys[1]);
		$this->_app1 = $request->getGet($keys[2]);
		$this->_app2 = $request->getGet($keys[3]);
	}

}