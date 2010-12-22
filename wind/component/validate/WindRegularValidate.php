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
	
	/**
	 * 在 $string 字符串中搜索与 $regExp 给出的正则表达式相匹配的内容。
	 * @param string $regExp  搜索的规则(正则)
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifall   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateByRegExp($regExp,$string,&$matches=array(),$ifall = false){
		if($ifAll){
			return preg_match_all($regExp,$string,$matches);
		}
		return preg_match($regExp,$string,$matches);
	}
	
	/**
	 * 验证是否是合法的email
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateEmail($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是合法的身份证号
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateIdCard($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/\d{17}[\d|X]|\d{15}/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是合法的URL
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateUrl($string,&$matches = array(),$ifAll = false){
		return $this->validateByRegExp("/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是中文
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateChinese($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/^[\u4e00-\u9fa5]+/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是html
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateHtml($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/<(.*)>.*|<(.*)\/>/",$string,$matches,$ifall);
	}
	
	
	/**
	 * 验证是否是双字节
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function validateDoubleType($string,&$matches=array(),$ifAll = false){
		return $this->validateByRegExp("/[^\x00-\xff]+/",$string,$matches,$ifall);
	}
}