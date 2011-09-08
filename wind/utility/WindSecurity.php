<?php
/**
 * 字符、路径过滤等安全处理
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSecurity {

	/**
	 * Convert special characters to HTML entities
	 * @param array $data
	 * @return array
	 */
	public static function escapeHTMLForArray($data) {
		$_tmp = array();
		$_charset = Wind::getApp()->getRequest()->getCharset();
		foreach ($data as $key => $value) {
			if (is_string($key))
				$key = htmlspecialchars($key, ENT_QUOTES, $_charset);
			if (is_string($value))
				$value = htmlspecialchars($value, ENT_QUOTES, $_charset);
			elseif (is_array($value))
				$value = self::escapeHTMLForArray($value);
			$_tmp[$key] = $value;
		}
		return $_tmp;
	}

	/**
	 * Convert special characters to HTML entities
	 * @param string $str
	 * @return string
	 */
	public static function escapeHTML($str) {
		if (is_array($str))
			return self::escapeHTMLForArray($str);
		return htmlspecialchars($str, ENT_QUOTES, Wind::getApp()->getResponse()->getCharset());
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
		if (!self::_escapePath($fileName, $ifCheck))
			throw new WindException('[utility.WindSecurity.escapePath] file name is illegal');
		return $fileName;
	}

	/**
	 * 目录转换
	 * @param string $dir
	 * @return string
	 */
	public static function escapeDir($dir) {
		$dir = strtr($dir, 
			array("'" => '', '#' => '', '=' => '', '`' => '', '$' => '', '%' => '', '&' => '', ';' => ''));
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
		$string = strtr($string, 
			array("\0" => '', "%00" => '', "\t" => '    ', '  ' => '&nbsp;&nbsp;', "\r" => '', "\r\n" => '', "\n" => '', 
				"%3C" => '&lt;', '<' => '&lt;', "%3E" => '&gt;', '>' => '&gt;', '"' => '&quot;', "'" => '&#39;'));
		return preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '/&(?!(#[0-9]+|[a-z]+);)/is'), 
			array('', '&amp;'), $string);
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
			$value = "'" . addslashes($value) . "'";
		} elseif (is_float($value)) {
			$value = (float) $value;
		} elseif (is_object($value) || is_array($value)) {
			$value = "'" . addslashes(serialize($value)) . "'";
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
		if (!$value || (!is_array($value) && !is_string($value) && !($value instanceof Traversable))) {return $value;}
		if (is_string($value)) {
			if (false === $gpc && true === $df) {return self::addSlashesForOutput($value);}
			if (false === $df && true === $gpc) {return self::addSlashesForInput($value);}
			return addslashes($value);
		}
		foreach ($value as $key => $_value) {
			$value[$key] = self::addSlashes($_value, $gpc, $df);
		}
		return $value;
	}

	/**
	 * 去除反 斜线
	 * @param mixed $array
	 * @return string
	 */
	public static function stripSlashes($value) {
		if (!$value)
			return $value;
		if (is_string($value))
			return stripslashes($value);
		if (!is_array($value) && !($value instanceof Traversable))
			return $value;
		foreach ($value as $key => $_value) {
			$value[$key] = self::stripSlashes($_value);
		}
		return $value;
	}

	/**
	 * 通用多类型混合转义函数
	 * @param $var
	 * @param $strip
	 * @param $isArray
	 * @return mixture
	 */
	public static function sqlEscape($var, $strip = true, $isArray = false) {
		if (is_array($var)) {
			if (!$isArray)
				return " '' ";
			foreach ($var as $key => $value) {
				$var[$key] = trim(self::sqlEscape($value, $strip));
			}
			return $var;
		} elseif (is_numeric($var)) {
			return " '" . $var . "' ";
		} else {
			return " '" . addslashes($strip ? stripslashes($var) : $var) . "' ";
		}
	}

	/**
	 * 通过","字符连接数组转换的字符
	 * @param $array
	 * @param $strip
	 * @return string
	 */
	public static function sqlImplode($array, $strip = true) {
		return implode(',', self::sqlEscape($array, $strip, true));
	}

	/**
	 * 组装单条 key=value 形式的SQL查询语句值 insert/update
	 * @param $array
	 * @param $strip
	 * @return string
	 */
	public static function sqlSingle($array, $strip = true) {
		if (!is_array($array))
			return '';
		$array = self::sqlEscape($array, $strip, true);
		$str = '';
		foreach ($array as $key => $val) {
			$str .= ($str ? ', ' : ' ') . self::sqlMetadata($key) . '=' . $val;
		}
		return $str;
	}

	/**
	 * 组装多条 key=value 形式的SQL查询语句 insert
	 * @param $array
	 * @param $strip
	 * @return string
	 */
	public static function sqlMulti($array, $strip = true) {
		if (!is_array($array)) {return '';}
		$str = '';
		foreach ($array as $val) {
			if (!empty($val) && is_array($val)) {
				$str .= ($str ? ', ' : ' ') . '(' . self::sqlImplode($val, $strip) . ') ';
			}
		}
		return $str;
	}

	/**
	 * 过滤SQL元数据，数据库对象(如表名字，字段等)
	 * @param $data 元数据
	 * @param $tlists 白名单
	 * @return string 经过转义的元数据字符串
	 */
	public static function sqlMetadata($data, $tlists = array()) {
		if (empty($tlists) || !in_array($data, $tlists)) {
			$data = str_replace(array('`', ' '), '', $data);
		}
		return ' `' . $data . '` ';
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
		if (strtr($tmpname, $tmparray) != $tmpname) {return false;}
		return true;
	}

}