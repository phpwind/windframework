<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
interface IWindConfig {
	
	/**
	 * 应用的入口地址
	 */
	const ROOTPATH = 'rootPath';
	
	
	const APPLICATIONS = 'applications';
	const APPLICATIONS_CLASS = 'class';
	
	const ERROR = 'error';
	const ERROR_ERRORACTION = 'errorAction';
	const ERROR_CLASS = 'class';
	
	/**
	 * 模快設置
	 */
	const MODULES = 'modules';
	const MODULE_PATH = 'path';
	const MODULE_TEMPLATE = 'template';
	const MODULE_CONTROLLER_SUFFIX = 'controllerSuffix';
	const MODULE_ACTION_SUFFIX = 'actionSuffix';
	const MODULE_METHOD = 'method';
	/**
	 * 过滤器链
	 */
	const FILTERS = 'filters';
	const FILTER_CLASS = 'class';
	
	/**
	 * 模板相关配置信息
	 * 1.模板文件存放路径
	 * 2.默认的模板文件名称
	 * 3.模板文件后缀名
	 * 4.视图解析器
	 * 5.模板文件的缓存路径
	 * 6.模板编译路径
	 */
	const TEMPLATE = 'templates';
	const TEMPLATE_DIR = 'dir';
	const TEMPLATE_DEFAULT = 'default';
	const TEMPLATE_EXT = 'ext';
	const TEMPLATE_RESOLVER = 'resolver';
	const TEMPLATE_ISCACHE = 'isCache';
	const TEMPLATE_CACHE_DIR = 'cacheDir';
	const TEMPLATE_COMPILER_DIR = 'compileDir';
	
	/**
	 * 模板引擎配置信息
	 */
	const VIEWER_RESOLVERS = 'viewerResolvers';
	
	/**
	 * 路由策略配置
	 */
	const ROUTER = 'router';
	const ROUTER_PARSER = 'parser';
	
	/**
	 * 路由解析器配置
	 */
	const ROUTER_PARSERS = 'routerParsers';
	const ROUTER_PARSERS_RULE = 'rule';
	const ROUTER_PARSERS_CLASS = 'class';
	
	/**
	 * @var 数据库配置
	 */
	const DATABASE = 'database';
	const DATABASE_CLASS = 'class';
}