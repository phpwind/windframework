<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 解析配置信息并根据配置信息创建路由解析器
 * 
 * 关于路由的相关配置
 * 路由解析器配置：使用哪一种路由解析器
 * 路由默认值的配置：路由的默认值
 * 
 * $config = array(
 * 'router' => array(
 * 'parser' => 'url',
 * 'defaultAction' => 'run', 
 * 'defaultController' => 'index', 
 * 'defaultApp1' => 'cms',       // 子应用模块, 这个参数为空则表示没有子应用模块；以应用目录为根目录 mode.cms
 * 'defaultApp2' => 'front',     // admin/front,这个参数为空则为front; 以应用目录/子应用目录为根目录 protected.controller 或者  admin.controller
 * ),
 * 'app' => array(
 * 'cms' => 'xxx.xxx.xxx',
 * ),
 * 'module' => array(
 * 'front' => '',
 * 'admin' => '',
 * ),
 * );
 * 
 * 路由规则，当接收到一个请求后，路由管理器会根据路由配置初始化一个路由解析器解析该请求
 * 解析后返回一个WRouterContext对象
 * 
 * 操作访问规则：
 * 1. $action 操作名称，方法名或者类名称，根据配置的不同会产生变化，唯一不变的是该参数指向一个具体的业务操作
 * 2. $controller 应用控制器名称，类名称，该参数指向一组操作的集合，可能是某个小的业务模块
 * 3. $app1 一级应用控制器目录
 * 4. $app2 二级应用控制器目录
 * 
 * 访问顺序 ：
 * $app1/$app2/$controller -> $action
 * 
 * 以上各参数都有默认值，如果未定义该参数，则使用默认值
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WRouterManager extends WRouter implements WContext {
	
	/**
	 * 根据配置文件，初始化路由信息
	 */
	static function init() {
		$config = array(
			'router' => array(
				'parser' => 'url'
			)
		);
		$routerContext = & self::createRouterParser($config['router'])->doParser();
		
		$rootPath = ''; //通过配置文件获得应用程序根目录的路径信息
		$path = '';
		if ($routerContext->app1) {
			$path .= $config['app'][$routerContext->app1];
		}
		if ($routerContext->app2) {
			$path .= $config['module'][$routerContext->app2];
		}
		if ($routerContext->controller) {
			$path .= $routerContext->controller;
		}
	
	}
	
	/**
	 * 解析路由配置文件信息
	 * @param array $config
	 */
	function _parserConfig($config) {

	}
	
	/**
	 * 获得路由解析器的路径信息
	 * 系统有一组默认的配置，框架级别的提供的路由解析路径
	 * 
	 * @param string $key
	 * @return string
	 */
	private function _getParserPath($key, $config = '') {
		$parser = array(
			'url' => 'router.parser.WUrlRouteParser'
		);
		if (!key_exists($key, $parser) && $config && $config['routerParser']) {
			$parser = (array) $config['routerParser'];
		}
		return key_exists($key, $parser) ? $parser[$key] : '';
	}
	
	/**
	 * 根据配置信息创建路由解析器
	 * @param array $config
	 * @return WRouteParser
	 */
	private function _createRouterParser($config) {
		$className = '';
		if (!$config['parser'])
			$config['parser'] = self::$_defaultParser;
		$path = self::_getParserPath($config['parser'], $config);
		if (file_exists($path)) {
			if (($pos = strpos($path, '.')) === false) {
				$className = $path;
			} else
				$className = substr($path, $pos + 1);
			WBasic::import($path);
		}
		return new $className();
	}
	
	/**
	 * 获得该类的静态单例对象
	 */
	public static function getInstance() {
		return WBasic::getInstance(__CLASS__);
	}

}