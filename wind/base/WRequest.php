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
interface WRequest {
	
	/**
	 * Returns the value of the named attribute as an <code>Array</code>,
	 * or <code>null</code> if no attribute of the given name exists. 
	 *
	 * @param name	a <code>String</code> specifying the name of 
	 * the attribute
	 * @return an <code>Object</code> containing the value 
	 * of the attribute, or <code>null</code> if
	 * the attribute does not exist
	 */
	public function getAttribute($name);
	
	/**
	 * Returns an array of <code>String</code> objects containing 
	 * all of the values the given request parameter has, or 
	 * <code>null</code> if the parameter does not exist.
	 * <p>If the parameter has a single value, the array has a length
	 * of 1.
	 *
	 * @param name	a <code>String</code> containing the name of 
	 * the parameter whose value is requested
	 *
	 * @return	an array of <code>String</code> objects 
	 * containing the parameter's values
	 */
	public function getParameterValues($name, $defaultValue = null);
	
	/**
	 * Returns the name and version of the protocol the request uses
	 * in the form <i>protocol/majorVersion.minorVersion</i>, for 
	 * example, HTTP/1.1. For HTTP servlets, the value
	 * returned is the same as the value of the CGI variable 
	 * <code>SERVER_PROTOCOL</code>.
	 *
	 * @return a <code>String</code> containing the protocol 
	 * name and version number
	 */
	public function getProtocol();
	
	/**
	 * Returns the host name of the server to which the request was sent.
	 * It is the value of the part before ":" in the <code>Host</code>
	 * header value, if any, or the resolved server name, or the server IP address.
	 *
	 * @return	a <code>String</code> containing the name 
	 * of the server
	 */
	public function getServerName();
	
	/**
	 * Returns the port number to which the request was sent.
	 * It is the value of the part after ":" in the <code>Host</code>
	 * header value, if any, or the server port where the client connection
	 * was accepted on.
	 *
	 * @return an integer specifying the port number
	 *
	 */
	public function getServerPort();
	
	/**
	 * Returns the Internet Protocol (IP) address of the client 
	 * or last proxy that sent the request.
	 * For HTTP servlets, same as the value of the 
	 * CGI variable <code>REMOTE_ADDR</code>.
	 *
	 * @return	a <code>String</code> containing the 
	 * IP address of the client that sent the request
	 *
	 */
	public function getRemoteAddr();
	
	/**
	 * Returns the fully qualified name of the client
	 * or the last proxy that sent the request.
	 * If the engine cannot or chooses not to resolve the hostname 
	 * (to improve performance), this method returns the dotted-string form of 
	 * the IP address. For HTTP servlets, same as the value of the CGI variable 
	 * <code>REMOTE_HOST</code>.
	 *
	 * @return a <code>String</code> containing the fully 
	 * qualified name of the client
	 *
	 */
	public function getRemoteHost();
	
	/**
	 *
	 * Returns a boolean indicating whether this request was made using a
	 * secure channel, such as HTTPS.
	 *
	 * @return a boolean indicating if the request was made using a
	 * secure channel
	 */
	public function isSecure();
	
	/**
	 * Returns the Internet Protocol (IP) source port of the client
	 * or last proxy that sent the request.
	 *
	 * @return	an integer specifying the port number
	 */
	public function getRemotePort();
	
	public function getRequestMethod();
}




