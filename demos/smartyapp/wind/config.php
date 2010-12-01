<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

$config = array(
	'view' => array(
		'ext' => 'phtml',
		'engine' => 'smarty',
		'viewPath' => R_P . '/templates',
		'tpl' => 'index',
		'cacheDir' =>  R_P . '/cache',//模板文件的缓存路径 
		'compileDir' => R_P . '/compile',//模板编译路径
	),
);