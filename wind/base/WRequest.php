<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-5
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 处理请求抽象基类
 * 如http请求
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
abstract class WRequest {
	protected static $_request = array();
	public abstract function getCookie();
	/**
	 * 获取POST的值
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getPost($key = '');
	/**
	 * 获取 HTTP GET的值
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getGet($key = '');
	/**
	 * 获取 HTTP SERVER的值
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getServer($key = '');
	/**
	 * 获取 HTTP REQUEST的值
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getRequest($key = '');
	/**
	 * 取得客户端使用的 HTTP 数据传输方法
	 * @return string
	 */
	public abstract function getHttpMethod();
	/**
	 * 取得http请求的 MIME 内容类型。
	 * @return string
	 */
	public abstract function getAcceptTypes();
	/**
	 * 取得客户端浏览器的原始用户代理信息。
	 * @return string
	 */
	public abstract function getUserAgent();
	/**
	 *验证HTTP连接中是否使用安全套接字 (ssl安全连接)
	 *@return boolean
	 */
	public abstract function IsSecureConnection();
	/**
	 * 取得请求页面的URI
	 * @return string 返回http请求页面的URI
	 */
	public abstract function getRequestUri();
	/**
	 * 取得 HTTP请求 查询字符串
	 */
	public abstract function getQuery();
	/**
	 * 取得http请求当前脚本文件所在的目录
	 * @return string;
	 */	
	public abstract function getFilePath();
	/**
	 * 取得http请求当前脚本文件的真实路径
	 * @return string
	 */
	public abstract function getFile();
	/**
	 * 取得http请求中原始的URL
	 * @return string
	 */
	public abstract function getRequestUrl();
	public abstract function getBaseUrl();
	/**
	 * 取得客户端上次请求的 URL地址
	 * @return string
	 */
	public abstract function getReferUrl();
	/**
	 * 取得http请求中的当前脚本文件名
	 * @return string
	 */
	public abstract function getScript();
	/**
	 * 取得服务器DNS
	 * @param $schema string
	 * @return string
	 */
	public abstract function getHost($schema = '');
	/**
	 * 取得http请求中的完整主机地址
	 * @return string
	 */
	public abstract function getUserHost();
	/**
	 * 取得http请求客户端中的IP地址
	 * @return string
	 */
	public abstract function getUserHostAddr();
	/**
	 * 取得http请求中服务器端主机地址
	 * @return string
	 */
	public abstract function getServerName();
	/**
	 *  取得http请求中服务器端主机地址的端口号
	 * @return string
	 */
	public abstract function getServerPort();
	/**
	 * 获取HTTP请求的客户端的浏览器相关信息
	 * @param string $userAgent 客户端浏览器的原始用户代理信息
	 * @return array
	 */
	public abstract function getUserBrowser();
	/**
	 * 返回http头信息
	 * @return  array;
	 */
	public abstract function getHeaders();
		
}




