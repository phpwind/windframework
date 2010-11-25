<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
interface IWindConfig {
	const APP = 'app';
	const APP_NAME = 'name';
	const APP_ROOTPATH = 'rootPath';
	const APP_CONFIG = 'configPath';

	const FILTERS = 'filters';
	const FILTER = 'filter';
	const FILTERNAME = 'filterName';
	const FILTERPATH = 'filterPath';
    
	const TEMPLATES = 'templates';
	const TEMPLATE = 'template';
	const TEMPLATEDIR = 'templateDir';
	const COMPILERDIR = 'compileDir';
	const CACHEDIR = 'cacheDir';
	const TEMPLATEEXT = 'templateExt';
	const ENGINE = 'engine';

	const ISOPEN = 'isOpen';
	const DESCRIBE = 'describe';
	
	const AAAAA = 'AAAAA';
	const bbbb = 'bbbb';


	const ATTRINAME = 'name';
	const GLOBALATTR = 'isGlobal';
	const MERGEATTR = 'isMerge';
	/**
	 * ”√”⁄
	 * @var unknown_type
	 */
	const PARSERARRAY = 'app, filters, templates, isOpen, describe, AAAAA, BBBBB';
}