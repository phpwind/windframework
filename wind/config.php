<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/*
 * 框架核心配置文件 <路由配置，应用配置，过滤器配置...>
 * 
 * */
$sysConfig = array(
	/* 保存过滤路径 */
	'filters' => array(),
	
	/* 应用路径配置 */
	'modules' => array(
		'default' => 'actionControllers'
	),
	
	/* 模板相关配置信息 */
	'view' => array(
		'viewPath' => 'template',  //模板文件路径
		'tpl' => 'index',  //默认的模板文件
		'engine' => 'default',  //default,smarty
		'ext' => 'htm' //模板文件后缀名
	), 
	
	/* 模板引擎配置信息 */
	'viewEngine' => array(
		'default' => 'WIND:core.WViewer', 
		'pw' => 'WIND:core.WPWViewer', 
		'smarty' => 'WIND:core.WSmartyViewer'
	),
	
	/*
	 * 这个配置选项有两个值，配置操作结构是基于WActionController或者基于WAction
	 * 基于WAction：则目录结构是actionControllers/controller/action.php
	 * 基于WActionController：则目录结构是actionControllers/controller.php
	 * */
	'controller' => 'WActionController',
	
	/* 路由策略配置 */
	'router' => array(
		'parser' => 'url'
	), 
	
	/* URL路由规则配置  */
	'urlRule' => array(
		'action' => 'run', 
		'controller' => 'index', 
		'module' => ''
	),
	 
	/* 路由解析器配置 */
	'routerParser' => array(
		'url' => 'WIND:core.WUrlRouter'
	)
);
