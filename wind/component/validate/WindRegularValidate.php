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
	
	public static function validateByRegExp($regExp,$string,&$matches=array(),$ifall = false){
		if($ifAll){
			return preg_match_all($regExp,$string,$matches);
		}
		return preg_match($regExp,$string,$matches);
	}
	
	public static function validateEmail($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",$string,$matches,$ifall);
	}
	
	public static function validateIdCard($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/\d{17}[\d|X]|\d{15}/",$string,$matches,$ifall);
	}
	
	public static function validateUrl($string,&$matches = array(),$ifAll = false){
		return $this->validateByRegExp("/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",$string,$matches,$ifall);
	}
	
	public static function validateChinese($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/^[\u4e00-\u9fa5]+/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是html
	 * @param string $string
	 * @return number
	 */
	public static function validateHtml($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/<(.*)>.*|<(.*)\/>/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是双字节,包括汉字
	 * @param string $string
	 * @return bo
	 */
	public static function validateDoubleType($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/[^\x00-\xff]+/",$string,$matches,$ifall);
	}
}