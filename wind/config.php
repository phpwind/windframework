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
	
	//保存过滤路径
	'filters' => array(
		'testFilter' => 'filter.TestFilter', 
		'test1Filter' => 'filter.Test1Filter'
	), 
	
	/* 应用配置 */
	'apps' => array(), 
	
	/* 路由策略配置 */
	'router' => array(
		'parser' => 'url'
	), 
	/* URL路由规则配置  */
	'urlRule' => array(
		'action' => 'run', 
		'controller' => 'index', 
		'app1' => 'controller1', 
		'app2' => ''
	), 
	/* 路由解析器配置 */
	'routerParser' => array(
		'url' => 'core.WUrlRouter'
	)
);
