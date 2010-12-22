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
		if(true === $ifAll){
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
	public static function hasEmail($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",$string,$matches,$ifall);
	}
	
	public static function isEmail($string){
		return 0 < preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/",$string);
	}
	
	
	
	/**
	 * 验证是否是合法的身份证号
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasIdCard($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/\d{17}[\d|X]|\d{15}/",$string,$matches,$ifall);
	}
	
	public static function isIdCard($string){
		return 0 < preg_match("/^(?:\d{17}[\d|X]|\d{15})$/",$string);
	}
	
	/**
	 * 验证是否是合法的URL
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasUrl($string,&$matches = array(),$ifAll = false){
		return self::validateByRegExp("/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",$string,$matches,$ifall);
	}
	
	public static function isUrl($string){
		return 0 < preg_match("/^(?:http(?:s)?:\/\/(?:[\w-]+\.)+[\w-]+(?:\:\d+)*+(?:\/[\w- .\/?%&=]*)?)$/",$string);
	}
	
	/**
	 * 验证是否是中文
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasChinese($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/[\x{4e00}-\x{9fa5}]+/u",$string,$matches,$ifall);
	}
	
	public static function isChinese($string){
		return 0 < preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$string);
	}
	
	/**
	 * 验证是否是html
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasHtml($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/<(.*)>.*|<(.*)\/>/",$string,$matches,$ifall);
	}
	
	
	/**
	 * 验证是否是双字节
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasDoubleType($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/[^\x00-\xff]+/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是合法的电话号码
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasTel($string,&$matches=array(),$ifAll = false){
		$regExp = "/(^(0\d{2})?-?(\d{7,8})$)|(^(0\d{3})?-?(\d{7,8})$)|(^(0\d{2})?-?(\d{8})-(\d+)$)|(^(0\d{3})?-?(\d{7})-(\d+)$)/";
		return self::validateByRegExp($regExp,$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是非负整数
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function isNonNegative($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/^\d+$/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是正数
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function isPositive ($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/^[1-9][0-9]*$/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是负数
	 * @param string $string   被搜索的 字符串
	 * @param array $matches   会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function isNegative($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/^-[1-9][0-9]*$/",$string,$matches,$ifall);
	}
	
	/**
	 * 验证是否是合法的ipv4地址
	 * @param string $string   被搜索的 字符串
	 * @param array $matches   会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasIP($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/(\d+)\.(\d+)\.(\d+)\.(\d+)/",$string,$matches,$ifall);
	}
	
	public static function isIP($string){
		return 0 < preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/',$string); 
	}
	
	/**
	 * 验证是否是客户端脚本
	 * @param string $string   被搜索的 字符串
	 * @param array $matches   会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public static function hasScript($string,&$matches=array(),$ifAll = false){
		return self::validateByRegExp("/<script(.*?)>([^\x00]*?)<\/script>/i",$string,$matches,$ifall);
	}
}