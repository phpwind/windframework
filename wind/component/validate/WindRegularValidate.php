<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindRegularValidate{
	public static function validateEmail($string){
		return preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",$string);
	}
	
	public static function validateIdCard($string){
		return preg_match("/\d{17}[\d|X]|\d{15}/",$string);
	}
	
	public static function validateUrl($string){
		return preg_match("/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",$string);
	}
	
	public static function validateChinese($string){
		return preg_match("/^[\u4e00-\u9fa5]+/",$string);
	}
	
	public static function ValidateHtml($string){
		return preg_match("/<(.*)>.*|<(.*)\/>/",$string);
	}
}