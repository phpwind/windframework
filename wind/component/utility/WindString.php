<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 字符串格式化
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindString {
	
	/**
	 * 截取字符串
	 * @param string $string 要截取的字符串编码
	 * @param int $start     开始截取
	 * @param int $length    截取的长度
	 * @param string $charset 原妈编码 
	 * @param boolean $dot    是否显示省略号
	 * @return string 截取后的字串
	 */
	public static function substr($string, $start, $length, $charset = 'UTF-8', $dot = false) {
		//TODO 
		if (in_array($charset, array('utf-8', 'UTF-8', 'utf8', 'UTF8'))) {
			return self::utf8_substr($string, $start, $length, $dot);
		} else {
			return self::gbk_substr($string, $start, $length, $dot);
		}
	}
	
	/**
	 * 求取字符串长度
	 * @param string $string  要计算的字符串编码
	 * @param string $charset 原始编码
	 * @return int
	 */
	public static function strlen($string, $charset = 'UTF-8') {
		$len = strlen($string);
		$i = $count = 0;
		while ($i < $len) {
			ord($string[$i]) > 129 ? in_array($charset, array('utf-8', 'UTF-8', 'utf8', 'UTF8')) ? $i += 3 : $i += 2 : $i++;
			$count++;
		}
		return $count;
	}
	
	/**
	 * 以utf8格式截取的字符串编码
	 * @param string $string  要截取的字符串编码
	 * @param int $start      开始截取
	 * @param int $length     截取的长度
	 * @param boolean $dot    是否显示省略号
	 * @return string
	 */
	public static function utf8_substr($string, $start, $length = null, $dot = false) {
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
	 * 以utf8求取字符串长度
	 * @param string $str     要计算的字符串编码
	 * @return number
	 */
	public static function utf8_strlen($str) {
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
	
	/* 以gbk格式截取的字符串编码
	 * @param string $string  要截取的字符串编码
	 * @param int $start      开始截取
	 * @param int $length     截取的长度
	 * @param boolean $dot    是否显示省略号
	 * @return string
	 */
	function gbk_substr($string, $start, $length = null, $dot = false) {
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
	 * 以gbk求取字符串长度
	 * @param string $str     要计算的字符串编码
	 * @return number
	 */
	public static function gbk_strlen($string) {
		$len = strlen($string);
		$i = $count = 0;
		while ($i < $len) {
			ord($string[$i]) > 129 ? $i += 2 : $i++;
			$count++;
		}
		return $count;
	}
	
	/**
	 * 变量导出为字符串
	 *
	 * @param mixed $input 变量
	 * @param string $indent 缩进
	 * @return string
	 */
	public static function varExport($input, $indent = '') {
		switch (gettype($input)) {
			case 'string':
				return "'" . str_replace(array("\\", "'"), array("\\\\", "\'"), $input) . "'";
			case 'array':
				$output = "array(\r\n";
				foreach ($input as $key => $value) {
					$output .= $indent . "\t" . self::varExport($key, $indent . "\t") . ' => ' . self::varExport($value, $indent . "\t");
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
}