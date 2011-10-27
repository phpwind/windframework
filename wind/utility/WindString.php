<?php
/**
 * 字符串格式化
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package utility
 */
class WindString {
	
	const UTF8 = 'utf8';
	
	const GBK = 'gbk';

	/**
	 * 截取字符串,支持字符编码,默认为utf-8
	 * 
	 * @param string $string 要截取的字符串编码
	 * @param int $start     开始截取
	 * @param int $length    截取的长度
	 * @param string $charset 原妈编码,默认为UTF8
	 * @param boolean $dot    是否显示省略号,默认为false
	 * @return string 截取后的字串
	 */
	public static function substr($string, $start, $length, $charset = self::UTF8, $dot = false) {
		switch (strtolower($charset)) {
			case self::GBK:
				$string = self::substrForGbk($string, $start, $length, $dot);
				break;
			default:
				$string = self::substrForUtf8($string, $start, $length, $dot);
				break;
		}
		return $string;
	}

	/**
	 * 求取字符串长度
	 * 
	 * @param string $string  要计算的字符串编码
	 * @param string $charset 原始编码,默认为UTF8
	 * @return int
	 */
	public static function strlen($string, $charset = self::UTF8) {
		$len = strlen($string);
		$i = $count = 0;
		while ($i < $len) {
			if (ord($string[$i]) <= 129)
				$i++;
			else
				switch (strtolower($charset)) {
					case self::UTF8:
						$i += 3;
						break;
					default:
						$i += 2;
						break;
				}
			$count++;
		}
		return $count;
	}

	/**
	 * 将变量的值转换为字符串
	 *
	 * @param mixed $input   变量
	 * @param string $indent 缩进,默认为''
	 * @return string
	 */
	public static function varToString($input, $indent = '') {
		switch (gettype($input)) {
			case 'string':
				return "'" . str_replace(array("\\", "'"), array("\\\\", "\\'"), $input) . "'";
			case 'array':
				$output = "array(\r\n";
				foreach ($input as $key => $value) {
					$output .= $indent . "\t" . self::varToString($key, $indent . "\t") . ' => ' . self::varToString(
						$value, $indent . "\t");
					$output .= ",\r\n";
				}
				$output .= $indent . ')';
				return $output;
			case 'boolean':
				return $input ? 'true' : 'false';
			case 'NULL':
				return 'NULL';
			case 'integer':
			case 'double':
			case 'float':
				return "'" . (string) $input . "'";
		}
		return 'NULL';
	}

	/**
	 * 将数据用json加密
	 *
	 * @param mixed $value 需要加密的数据
	 * @param string $charset 字符编码
	 * @return string 加密后的数据
	 */
	public static function jsonEncode($value, $charset = self::UTF8) {
		Wind::import('Wind:utility.WindJson');
		return WindJson::encode($value, $charset);
	}

	/**
	 * 将json格式数据解密
	 *
	 * @param string $value 待解密的数据
	 * @param string $charset 解密后字符串编码
	 * @return mixed 解密后的数据
	 */
	public static function jsonDecode($value, $charset = self::UTF8) {
		Wind::import('Wind:utility.WindJson');
		return WindJson::decode($value, true, $charset);
	}

	/**
	 * 以utf8格式截取的字符串编码
	 * 
	 * @param string $string  要截取的字符串编码
	 * @param int $start      开始截取
	 * @param int $length     截取的长度，默认为null，取字符串的全长
	 * @param boolean $dot    是否显示省略号，默认为false
	 * @return string
	 */
	public static function substrForUtf8($string, $start, $length = null, $dot = false) {
		if (empty($string)) return '';
		$strlen = strlen($string);
		$length = $length ? (int) $length : $strlen;
		$substr = '';
		$chinese = $word = 0;
		for ($i = 0, $j = 0; $i < (int) $start; $i++) {
			if (0xa0 < ord(substr($string, $j, 1))) {
				$chinese++;
				$j += 2;
			} else {
				$word++;
			}
			$j++;
		}
		$start = $word + 3 * $chinese;
		for ($i = $start, $j = $start; $i < $start + $length; $i++) {
			if (0xa0 < ord(substr($string, $j, 1))) {
				$substr .= substr($string, $j, 3);
				$j += 2;
			} else {
				$substr .= substr($string, $j, 1);
			}
			$j++;
		}
		(strlen($substr) < $strlen) && $dot && $substr .= "...";
		return $substr;
	}

	/**
	 * 以gbk格式截取的字符串编码
	 * 
	 * @param string $string  要截取的字符串编码
	 * @param int $start      开始截取
	 * @param int $length     截取的长度，默认为null，取字符串的全长
	 * @param boolean $dot    是否显示省略号，默认为false
	 * @return string
	 */
	public static function substrForGbk($string, $start, $length = null, $dot = false) {
		if (empty($string) || !is_int($start) || ($length && !is_int($length))) {
			return '';
		}
		$strlen = strlen($string);
		$length = $length ? $length : $strlen;
		$substr = '';
		$chinese = $word = 0;
		for ($i = 0, $j = 0; $i < $start; $i++) {
			if (0xa0 < ord(substr($string, $j, 1))) {
				$chinese++;
				$j++;
			} else {
				$word++;
			}
			$j++;
		}
		$start = $word + 2 * $chinese;
		for ($i = $start, $j = $start; $i < $start + $length; $i++) {
			if (0xa0 < ord(substr($string, $j, 1))) {
				$substr .= substr($string, $j, 2);
				$j++;
			} else {
				$substr .= substr($string, $j, 1);
			}
			$j++;
		}
		(strlen($substr) < $strlen) && $dot && $substr .= "...";
		return $substr;
	}

	/**
	 * 以utf8求取字符串长度
	 * 
	 * @param string $str     要计算的字符串编码
	 * @return int
	 */
	public static function strlenForUtf8($str) {
		$i = $count = 0;
		$len = strlen($str);
		while ($i < $len) {
			$chr = ord($str[$i]);
			$count++;
			$i++;
			if ($i >= $len) break;
			if ($chr & 0x80) {
				$chr <<= 1;
				while ($chr & 0x80) {
					$i++;
					$chr <<= 1;
				}
			}
		}
		return $count;
	}

	/**
	 * 以gbk求取字符串长度
	 * 
	 * @param string $str     要计算的字符串编码
	 * @return int
	 */
	public static function strlenForGbk($string) {
		$len = strlen($string);
		$i = $count = 0;
		while ($i < $len) {
			ord($string[$i]) > 129 ? $i += 2 : $i++;
			$count++;
		}
		return $count;
	}
}