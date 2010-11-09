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
	
 	//保存过滤规则
	'filterChain' => array( 
		'WInput' => array()
	), 
	//保存过滤路径
	'filters' => array(
		'WInput' => 'filter.WInput.php',
	), 
	
	'controllers' => array(
		'controller1' => 'www.app.controller', 
		'controller2' => 'www.app.controller.subcont'
	), 
	
	'router' => array(
		'parser' => 'url'
	), 
	'urlRule' => array(
		'action' => 'run', 
		'controller' => 'index', 
		'app1' => 'controller1', 
		'app2' => ''
	), 
	'routerParser' => array(
		'url' => 'router.parser.WUrlRouteParser.php'
	)
);
