<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
interface WindConfigImpl {
	const app = 'app';
	const appName = 'appName';
	const appPath = 'appPath';
	const appConfig = 'appConfig';
	
	const isOpen = 'isOpen';
	const describe = 'describe';
	
	const filters = 'filters';
	const filter = 'filter';
	const filterName = 'filterName';
	const filterPath = 'filterPath';
	
	const template = 'template';
	const templateDir = 'templateDir';
	const compileDir = 'compileDir';
	const cacheDir = 'cacheDir';
	const templateExt = 'templateExt';
	const engine = 'engine';
	
	const urlRule = 'urlRule';
	const routerPase = 'routerPase';
}