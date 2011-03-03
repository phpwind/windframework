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
class WindValidator {
	
	public function validate($input, $validator) {
		return $this->$validator($input);
	}
	
	/**
	 * 在 $string 字符串中搜索与 $regExp 给出的正则表达式相匹配的内容。
	 * @param string $regExp  搜索的规则(正则)
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function validateByRegExp($regExp, $string, &$matches = array(), $ifAll = false) {
		if (true === $ifAll) {
			return preg_match_all($regExp, $string, $matches);
		}
		return preg_match($regExp, $string, $matches);
	}
	
	/**
	 * 验证是否是有合法的email
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasEmail($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是合法的email
	 * @param string $string
	 * @return boolean
	 */
	public function isEmail($string) {
		return 0 < preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $string);
	}
	
	/**
	 * 验证是否有合法的身份证号
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasIdCard($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp("/\d{17}[\d|X]|\d{15}/", $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是合法的身份证号
	 * @param string $string
	 * @return boolean
	 */
	public function isIdCard($string) {
		return 0 < preg_match("/^(?:\d{17}[\d|X]|\d{15})$/", $string);
	}
	
	/**
	 * 验证是否有合法的URL
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasUrl($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp('/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是合法的url
	 * @param string $string
	 * @return boolean
	 */
	public function isUrl($string) {
		return 0 < preg_match('/^(?:http(?:s)?:\/\/(?:[\w-]+\.)+[\w-]+(?:\:\d+)*+(?:\/[\w- .\/?%&=]*)?)$/', $string);
	}
	
	/**
	 * 验证是否有中文
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasChinese($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp('/[\x{4e00}-\x{9fa5}]+/u', $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是中文
	 * @param string $string
	 * @return boolean
	 */
	public function isChinese($string) {
		return 0 < preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $string);
	}
	
	/**
	 * 验证是否有html标记
	 * @param string $string  被搜索的 字符串
	 * @param array $matches 会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasHTML($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp('/<(.*)>.*|<(.*)\/>/', $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是合法的html标记
	 * @param string $string
	 * @return boolean
	 */
	public function isHTML($string) {
		return 0 < preg_match('/^<(.*)>.*|<(.*)\/>$/', $string);
	}
	
	/**
	 * 验证是否是非负整数
	 * @param string $string  被搜索的 字符串
	 * @return number
	 */
	public function isNonNegative($string) {
		return 0 < preg_match("/^\d+$/", $string);
	}
	
	/**
	 * 验证是否是正数
	 * @param string $string  被搜索的 字符串
	 * @return number
	 */
	public function isPositive($string) {
		return 0 < preg_match('/^[1-9][0-9]*$/', $string);
	}
	
	/**
	 * 验证是否是负数
	 * @param string $string   被搜索的 字符串
	 * @return number
	 */
	public function isNegative($string) {
		return 0 < preg_match('/^-[1-9][0-9]*$/', $string);
	}
	
	/**
	 * 验证是否有合法的ipv4地址
	 * @param string $string   被搜索的 字符串
	 * @param array $matches   会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasIP($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是合法的IP
	 * @param string $string
	 * @return boolean
	 */
	public function isIP($string) {
		return 0 < preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $string);
	}
	
	/**
	 * 验证是否有客户端脚本
	 * @param string $string   被搜索的 字符串
	 * @param array $matches   会被搜索的结果
	 * @param boolean $ifAll   是否进行全局正则表达式匹配
	 * @return number
	 */
	public function hasScript($string, &$matches = array(), $ifAll = false) {
		return self::validateByRegExp('/<script(.*?)>([^\x00]*?)<\/script>/', $string, $matches, $ifAll);
	}
	
	/**
	 * 验证是否是合法的客户端脚本
	 * @param string $string
	 * @return boolean
	 */
	public function isScript($string) {
		return 0 < preg_match('/<script(?:.*?)>(?:[^\x00]*?)<\/script>/', $string);
	}
	
	public function isInt($value) {
		return is_int($value);
	}
	
	public function isFloat($value) {
		return is_float($value);
	}
	
	public function isArray($value) {
		return is_array($value);
	}
	
	public function isBool($value) {
		return is_bool($value);
	}
	
	public function isEmpty($value) {
		return empty($value);
	}
	
	/**
	 * required验证，如果输入的值为：空字符串，empty，null则返回false
	 * @param string $value
	 * @return boolean
	 */
	public function isRequired($value) {
		if (empty($value)) return false;
		if ($value === null) return false;
		return true;
	}
	
	/**
	 * 验证字符串的长度
	 * @param string $string 要验证的字符串
	 * @param string $length 指定的合法的长度
	 * @param string $charset 字符编码
	 * @return boolean
	 */
	public function isLegalLength($string, $length, $charset = 'utf8') {
		L::import('WIND:component.utility.WindString');
		return WindString::strlen($string, $charset) > (int) $length;
	}

}