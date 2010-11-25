<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
interface IWindConfig {
	/**
	 * 用于指定在程序解析中需要解析一级标签
	 */
	const PARSERARRAY = 'app, filters, view, viewerResolver, router, urlRule, routerParser';
	
	/**
	 * 应用配置信息
	 */
	const APP = 'app';
	const APP_NAME = 'name';
	const APP_ROOTPATH = 'rootPath';
	const APP_CONFIG = 'configPath';
    
	/**
	 * 过滤器链
	 */
	const FILTERS = 'filters';
	const FILTER = 'filter';
	const FILTER_NAME = 'filterName';
	const FILTER_PATH = 'filterPath';
    
	/**
	 * 模板相关配置信息
	 * 1.模板文件存放路径
	 * 2.默认的模板文件名称
	 * 3.模板文件后缀名
	 * 4.视图解析器
	 * 5.模板文件的缓存路径
	 * 6.模板编译路径
	 */
	const TEMPLATE = 'view';
	const TEMPLATE_DIR = 'templatePath';
	const TEMPLATE_NAME = 'templateName';
	const TEMPLATE_EXT = 'templateExt';
	const RESOLVER = 'resolver';
	const ISCACHE = 'isCache';
	const CACHE_DIR = 'cacheDir';
	const COMPILER_DIR = 'compileDir';
	
    /**
     * 模板引擎配置信息
     */
	const VIEWER_RESOLVER = 'viewerResolver';
	const RESOLVER_DEFAULT = 'default';
	const PHPWIND = 'pw';

	/**
	 * 路由策略配置
	 */
	const ROUTER = 'router';
	const PARSER = 'parser';
	
	/**
	 * URL路由规则配置
	 */
	const URLRULE = 'urlRule';
	const ACTION = 'action';
	const CONTROLLER = 'controller';
	const MODULE = 'module';
	
	/**
	 * 路由解析器配置
	 */
	const ROUTER_PARSER = 'routerParser';
	const URL = 'url';
	
	/**
	 * 定义允许拥有的属性
	 * name: 可以定义一些列的item中每一个item的名字以区分每一个
	 * isGlobal: 如果添加上该属性，则该标签将在解析完成之后被提出放置在全局缓存中 -----只作用于一级标签
	 * isMerge: 如果添加上该属性，则该标签将被在解析后进行合并 -----只作用于一级标签
	 */
	const ATTRNAME = 'name';
	const GLOBALATTR = 'isGlobal';
	const MERGEATTR = 'isMerge';
}