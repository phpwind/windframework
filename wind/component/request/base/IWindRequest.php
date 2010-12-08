<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 处理请求抽象基类
 * 如http请求
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package
 */
interface IWindRequest {
	const REQUEST_TYPE_WEB = 'web';
	const REQUEST_TYPE_COMMAND = 'command';
	
	const TYPE_GET = 'get';
	const TYPE_POST = 'post';
	const TYPE_COOKIE = 'cookie';
	
}




