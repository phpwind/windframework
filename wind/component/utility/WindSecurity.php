<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 字符、路径过滤等安全处理
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSecurity {
	/**
	 * html转换输出
	 * @param $param
	 * @return string
	 */
	public static function escapeHTML($str) {
		return htmlspecialchars($str, ENT_QUOTES);
	}
	/**
	 * 过滤标签
	 * @param $param
	 * @return string
	 */
	public static function stripTags($str, $allowTags = "") {
		return strip_tags($str, $allowTags);
	}
	/**
	 * 路径转换
	 * @param $fileName
	 * @param $ifCheck
	 * @return string
	 */
	public static function escapePath($fileName, $ifCheck = true) {
		if (!self::_escapePath($fileName, $ifCheck)) {
			throw new WindException('file name is illegal');
		}
		return $fileName;
	}
	/**
	 * 目录转换
	 * @param string $dir
	 * @return string
	 */
	public static function escapeDir($dir) {
		$dir = strtr($dir, array("'" => '', '#' => '', '=' => '', '`' => '', '$' => '', '%' => '', '&' => '', ';' => ''));
		return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $dir), '/');
	}
	/**
	 * 通用多类型转换
	 * @param  mixed $value
	 * @return mixed
	 */
	public static function escapeChar($value) {
		if (is_array($value)) {
			foreach ($value as $key => $sub) {
				$value[$key] = self::escapeChar($sub);
			}
		} elseif (is_int($value)) {
			$value = (int) $value;
		} elseif (is_string($value)) {
			$value = self::escapeString($value);
		}
		return $value;
	}
	/**
	 * 字符转换
	 * @param string $string
	 * @return string
	 */
	public static function escapeString($string) {
		$string = strtr($string, array("\0" => '', "%00" => '', "\t" => '    ', '  ' => '&nbsp;&nbsp;', "\r" => '', 
			"\r\n" => '', "\n" => '', "%3C" => '&lt;', '<' => '&lt;', "%3E" => '&gt;', '>' => '&gt;', '"' => '&quot;', 
			"'" => '&#39;'));
		return preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '/&(?!(#[0-9]+|[a-z]+);)/is'), array('', 
			'&amp;'), $string);
	}
	
	/**
	 * 该函数可用于转义拥有特殊意义的字符，比如 SQL 中的 ( )、[ ] 以及 *。
	 * @param string $string
	 * @return string
	 */
	public static function quotemeta($string) {
		return quotemeta($string);
	}
	/**
	 * 对字符串转义
	 * @param string $value
	 * @return string
	 */
	public function checkInputValue($value, $key = '') {
		if (is_int($value)) {
			$value = (int) $value;
		} elseif (is_string($value)) {
			$value = " '" . addslashes($value) . "' ";
		} elseif (is_float($value)) {
			$value = (float) $value;
		} elseif (is_object($value) || is_array($value)) {
			$value = " '" . addslashes(serialize($value)). "' ";
		}
		return $value;
	}
	/**
	 * 对cookie/post/get方式的值添加反斜线
	 * @param string $str
	 * @return string
	 */
	public static function addSlashesForInput($str) {
		if (!get_magic_quotes_gpc()) {
			$str = addslashes($str);
		}
		return $str;
	}
	
	/**
	 * 对从db或者file里面读取的内容添加反斜线
	 * @return string
	 */
	public static function addSlashesForOutput($str) {
		if (!get_magic_quotes_runtime()) {
			$str = addslashes($str);
		}
		return $str;
	}
	
	/**
	 * 添加反斜线,转义字符
	 * @param mixed $value 要处理的数组
	 * @param boolean $gpc 是否是get/cookie/post传递过来的值
	 * @param boolean $df  是否是database/file传递过来的值
	 * @return string
	 */
	public static function addSlashes($value, $gpc = false, $df = false) {
		if (!$value || (!is_array($value) && !is_string($value) && !($value instanceof Traversable) )) {
			return $value;
		}
		if(is_string($value)){
			if (false === $gpc && true === $df) {
				return self::addSlashesForOutput($value);
			}
			if (false === $df && true === $gpc) {
				return self::addSlashesForInput($value);
			}
			return addslashes($value);
		}
		foreach($value as $key=>$_value){
			$value[$key] == self::addSlashes($value,$gpc,$df);
		}
		return $value;
	}
	/**
	 * 去除反 斜线
	 * @param mixed $array
	 * @return string
	 */
	public static function stripSlashes($value) {
		if (!$value) return $value;
		if (is_string($value)) return stripslashes($value);
		if (!is_array($value) || !($value instanceof Traversable)) return $value;
		foreach ($value as $key => $_value) {
			$value[$key] = self::stripSlashes($_value);
		}
		return $value;
	}
	/**
	 * 私用路径转换
	 * @param string  $fileName
	 * @param boolean $ifCheck
	 * @return boolean
	 */
	private static function _escapePath($fileName, $ifCheck = true) {
		$tmpname = strtolower($fileName);
		$tmparray = array('://' => '', "\0" => '');
		$ifCheck && $tmparray['..'] = '';
		if (strtr($tmpname, $tmparray) != $tmpname) {
			return false;
		}
		return true;
	}
}