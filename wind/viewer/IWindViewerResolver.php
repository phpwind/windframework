<?php
/**
 * 视图渲染器接口类
 * 
 * 视图渲染器接口,主要定义了两个接口方法<i>windAssign</i>和<i>windFetch</i><pre>
 * IWindViewerResolver接口是框架定义的基础的视图渲染器接口,通过实现该接口类来自定义视图渲染器
 * <i>WindViewerResolver</i>类是该接口的基本实现
 * </pre>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 */
interface IWindViewerResolver {

	/**
	 * 设置视图变量设置进当前模板
	 * 
	 * @param array|string|object $vars
	 * @param string $key 可选 默认值为空
	 * @return void
	 */
	public function windAssign($vars, $key = '');

	/**
	 * 获取模板内容与变量信息
	 * 
	 * @param string $template 可选 默认值为空
	 * @return void
	 */
	public function windFetch($template = '');

}