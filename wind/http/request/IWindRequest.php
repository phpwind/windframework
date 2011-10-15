<?php
/**
 * request接口定义
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package http
 * @subpackage request
 */
interface IWindRequest {
	/**
	 * 常量定义GET的别名
	 *
	 * @var string
	 */
	const INPUT_TYPE_GET = 'get';
	/**
	 * 常量定义POST的别名
	 *
	 * @var string
	 */
	const INPUT_TYPE_POST = 'post';
	/**
	 * 常量定义COOKIE的别名
	 *
	 * @var string
	 */
	const INPUT_TYPE_COOKIE = 'cookie';
}




