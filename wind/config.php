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

	/* 
	 * 模板相关配置信息
	 * 1.模板文件存放路径
	 * 2.默认的模板文件名称
	 * 3.模板文件后缀名
	 * 4.视图解析器
	 * 5.模板文件的缓存路径
	 * 6.模板编译路径
	 *  */
	'view' => array(
		'templatePath' => 'template',
		'templateName' => 'index',  
		'templateExt' => 'htm',
		'resolver' => 'default',
		'isCache' => 'false',
		'cacheDir' => 'cache',
		'compileDir' => 'compile',
	),
	/* 模板引擎配置信息 */
	'viewerResolver' => array(
		'default' => 'WIND:component.viewer.WindViewer',
		'pw' => 'WIND:component.viewer.WindPWViewer',
		'smarty' => 'libs.WSmarty',
	),

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
		'url' => 'WIND:component.router.WindUrlBasedRouter'
	)
);
