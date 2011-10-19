<?php
/**
 * JSON处理类
 * 
 * 提供两个公用静态方法encode()、decode()
 * <ul>
 * <li>对数据进行加密方法：encode($value, $charset)，$charset参数指定传入参数的编码方式，返回的json串为标准的UNICODE编码
 * <li>对json串进行解密方法：decode($str, $useArray, $charset)，$charset参数指定返回的解密数据的编码方式，传入的json串须为标准的UNICODE编码，$useArray为true的时候，将返回 array 而非 object 
 * </ul>
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class WindJson {
	
	const UTF8 = 'UTF-8';
	/**
	 * 记录状态用常量
	 * 
	 * @var int
	 */
	const JSON_SLICE = 1;
	/**
	 * 记录状态用常量
	 * 
	 * @var int
	 */
	const JSON_IN_STR = 2;
	/**
	 * 记录状态用常量
	 * 
	 * @var int
	 */
	const JSON_IN_ARR = 4;
	/**
	 * 记录状态用常量
	 * 
	 * @var int
	 */
	const JSON_IN_OBJ = 8;
	/**
	 * 记录状态用常量
	 * 
	 * @var int
	 */
	const JSON_IN_CMT = 16;
	
	
	/**
	 * 将数据用json加密
	 * 
	 * @param mixed $value 要加密的值
	 * @param string $charset 传入数据使用的编码，默认为utf-8
	 * @return string 返回的string是以UNICODE进行编码
	 */
	public static function encode($value, $charset = self::UTF8) {
		switch (gettype($value)) {
			case 'boolean':
				return $value ? 'true' : 'false';
			case 'NULL':
				return 'null';
			case 'integer':
				return (int) $value;
			case 'double':
			case 'float':
				return (float) $value;
			case 'string':
				if (self::UTF8 !== ($enc = strtoupper($charset))) {
					$value = iconv($enc, self::UTF8, $value);
				}
				$ascii = '';
				$strlen = strlen($value);
				for ($c = 0; $c < $strlen; ++$c) {
					$b = $value{$c};
					$ordVar = ord($value{$c});
					if (0x08 == $ordVar) {
						$ascii .= '\b';
					} elseif (0x09 == $ordVar) {
						$ascii .= '\t';
					} elseif (0x0A == $ordVar) {
						$ascii .= '\n';
					} elseif (0x0C == $ordVar) {
						$ascii .= '\f';
					} elseif (0x0D == $ordVar) {
						$ascii .= '\r';
					} elseif (in_array($ordVar, array(0x22, 0x2F, 0x5C))) {
						$ascii .= '\\' . $value{$c};
					} elseif (0x20 <= $ordVar && 0x7F >= $ordVar) {
						$ascii .= $value{$c}; //ASCII
					} elseif (0xC0 == ($ordVar & 0xE0)) {
						$char = pack('C*', $ordVar, ord($value{++$c}));
						$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
					} elseif (0xE0 == ($ordVar & 0xF0)) {
						$char = pack('C*', $ordVar, ord($value{++$c}), ord($value{++$c}));
						$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
					} elseif (0xF0 == ($ordVar & 0xF8)) {
						$char = pack('C*', $ordVar, ord($value{++$c}), ord($value{++$c}), ord($value{++$c}));
						$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
					} elseif (0xF8 == ($ordVar & 0xFC)) {
						$char = pack('C*', $ordVar, ord($value{++$c}), ord($value{++$c}), ord($value{++$c}), ord($value{++$c}));
						$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
					} elseif (0xFC == ($ordVar & 0xFE)) {
						$char = pack('C*', $ordVar, ord($value{++$c}), ord($value{++$c}), ord($value{++$c}), ord($value{++$c}), ord($value{++$c}));
						$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
					}
				}
				return '"' . $ascii . '"';
			case 'array':
				if (is_array($value) && count($value) && (array_keys($value) !== range(0, sizeof($value) - 1))) {
					return '{' . join(',', array_map(array('WindJson', 'nameValue'), array_keys($value), array_values($value), array_fill(0, sizeof($value), $charset))) . '}';
				}
				return '[' . join(',', array_map(array('WindJson', 'encode'), $value, array_fill(0, sizeof($value), $charset))) . ']';
			case 'object':
				if ($value instanceof Traversable) {
					$vars = array();
					foreach ($value as $k => $v) {
						$vars[$k] = $v;
					}
				} else {
					$vars = get_object_vars($value);
				}
				return '{' . join(',', array_map(array('WindJson', 'nameValue'), array_keys($vars), array_values($vars), array_fill(0, sizeof($vars), $charset))) . '}';
			default:
				return '';
		}
	}
	
	/**
	 * 将json格式数据解密
	 * 
	 * @param string $str 传入的json串必须以UNICODE编码
	 * @param boolean $useArray 返回数组或对象
	 * @param string $charset 设置返回值的编码，默认为utf-8
	 * @return mixed
	 */
	public static function decode($str, $useArray = true, $charset = self::UTF8) {
		$str = strtolower(self::reduceString($str));
		
		switch ($str){
			case 'true':
				return true;

			case 'false':
				return false;

			case 'null':
				return null;
				
			default:
				if (is_numeric($str)) {
					return (float)$str == (integer)$str ? (integer) $str : (float) $str;
					
				}elseif(preg_match('/^("|\').+(\1)$/s', $str, $matche) && $matche[1] == $matche[2]){
					$delim = substr($str, 0, 1);
					$chrs = substr($str, 1, -1);
					$decodeStr = '';
					for ($c = 0,$length = strlen($chrs); $c < $length; ++$c) {
						$compare = substr($chrs, $c, 2);
						$ordCode = ord($chrs{$c});
						if('\b' == $compare){
							$decodeStr .= chr(0x08);
							++$c;
						}elseif('\t' == $compare){
							$decodeStr .= chr(0x09);
							++$c;
						}elseif('\n' == $compare){
							$decodeStr .= chr(0x0A);
							++$c;
						}elseif('\f' == $compare){
							$decodeStr .= chr(0x0C);
							++$c;
						}elseif('\r' == $compare){
							$decodeStr .= chr(0x0D);
							++$c;
						}elseif(in_array($compare,array('\\"','\\\'','\\\\','\\/'))){
							if (('"' == $delim  && '\\\'' != $compare) || ("'" == $delim && '\\"' != $compare)) {
								$decodeStr .= $chrs{++$c};
							}
						}elseif(preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6))){
							$utf16 = chr(hexdec(substr($chrs, ($c + 2), 2))) . chr(hexdec(substr($chrs, ($c + 4), 2)));
							$decodeStr .= self::utf16beToUTF8($utf16);
							$c += 5;
						}elseif(0x20 <= $ordCode &&  0x7F >= $ordCode){
							$decodeStr .= $chrs{$c};
						}elseif(0xC0 == ($ordCode & 0xE0)){
							$decodeStr .= substr($chrs, $c, 2);
							++$c;
						}elseif(0xE0 == ($ordCode & 0xF0)){
							$decodeStr .= substr($chrs, $c, 3);
							$c += 2;
						}elseif(0xF0 == ($ordCode & 0xF8)){
							$decodeStr .= substr($chrs, $c, 4);
							$c += 3;
						}elseif(0xF8 == ($ordCode & 0xFC)){
							$decodeStr .= substr($chrs, $c, 5);
							$c += 4;
						}elseif(0xFC == ($ordCode & 0xFE)){
							$decodeStr .= substr($chrs, $c, 6);
							$c += 5;
						}
					}
					if (self::UTF8 !== ($enc = strtoupper($charset))) {
						$decodeStr = iconv(self::UTF8, $enc, $decodeStr);
					}
					return $decodeStr;
					
				}elseif(preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)){
					if ('[' == $str{0}) {
						$stk = array(self::JSON_IN_ARR);
						$arr = array();
					} else {
						$obj = $useArray ? array() : new stdClass();
						$stk = array(self::JSON_IN_OBJ);
					}
					array_push($stk, array('what' => self::JSON_SLICE, 'where' => 0, 'delim' => false));
					$chrs = substr($str, 1, -1);
					$chrs = self::reduceString($chrs);
					if ('' == $chrs) {
						return self::JSON_IN_ARR == reset($stk) ? $arr : $obj;
					}
					for ($c = 0,$length = strlen($chrs); $c <= $length; ++$c) {
						$top = end($stk);
						$substr_chrs_c_2 = substr($chrs, $c, 2);
						if (($c == $length) || (($chrs{$c} == ',') && ($top['what'] == self::JSON_SLICE))) {
							$slice = substr($chrs, $top['where'], ($c - $top['where']));
							array_push($stk, array('what' => self::JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
							if (reset($stk) == self::JSON_IN_ARR) {
								array_push($arr, self::decode($slice, $useArray, $charset));
							} elseif (reset($stk) == self::JSON_IN_OBJ) {
								if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									$key = self::decode($parts[1], $useArray, $charset);
									$useArray ? $obj[$key] = self::decode($parts[2], $useArray, $charset) : $obj->$key = self::decode($parts[2], $useArray, $charset);
								} elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									$useArray ? $obj[$parts[1]] = self::decode($parts[2], $useArray, $charset) : $obj->$parts[1] = self::decode($parts[2], $useArray, $charset);
								}
							}
						
						} elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != self::JSON_IN_STR)) {
							array_push($stk, array('what' => self::JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));
						} elseif (($chrs{$c} == $top['delim']) && ($top['what'] == self::JSON_IN_STR) && (($chrs{$c - 1} != "\\") || ($chrs{$c - 1} == "\\" && $chrs{$c - 2} == "\\"))) {
							array_pop($stk);
						} elseif (($chrs{$c} == '[') && in_array($top['what'], array(self::JSON_SLICE, 
							self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
							array_push($stk, array('what' => self::JSON_IN_ARR, 'where' => $c, 'delim' => false));
						} elseif (($chrs{$c} == ']') && ($top['what'] == self::JSON_IN_ARR)) {
							array_pop($stk);
						} elseif (($chrs{$c} == '{') && in_array($top['what'], array(self::JSON_SLICE, 
							self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
							array_push($stk, array('what' => self::JSON_IN_OBJ, 'where' => $c, 'delim' => false));
						} elseif (($chrs{$c} == '}') && ($top['what'] == self::JSON_IN_OBJ)) {
							array_pop($stk);
						} elseif (($substr_chrs_c_2 == '/*') && in_array($top['what'], array(self::JSON_SLICE, 
							self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
							array_push($stk, array('what' => self::JSON_IN_CMT, 'where' => ++$c, 'delim' => false));
						} elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == self::JSON_IN_CMT)) {
							array_pop($stk);
							for ($i = $top['where']; $i <= ++$c; ++$i){
								$chrs = substr_replace($chrs, ' ', $i, 1);
							}
						}
					
					}
					if (self::JSON_IN_ARR == reset($stk)) {
						return $arr;
					} elseif (self::JSON_IN_OBJ == reset($stk)) {
						return $obj;
					}
					return false;
				}
		}
	}
	
	/**
	 * utf16BE编码转为utf8
	 *
	 * @param string $str
	 * @return string
	 */
	protected static function reduceString($str) {
		return trim(preg_replace(array(
		'#^\s*//(.+)$#m', 
		'#^\s*/\*(.+)\*/#Us', 
		'#/\*(.+)\*/\s*$#Us'), 
		'', $str));
	}
	
	/**
	 * utf16BE编码转为utf8
	 *
	 * @param string $str
	 * @return string
	 */
	protected static function utf16beToUTF8(&$str) {
		$str = unpack('n*', $str);
		$utf8 = '';
		foreach ($str as $unicode) {
			if ($unicode < 128) {
				$utf8 .= chr($unicode);
			} elseif ($unicode < 2048) {
				$utf8 .= chr(192 + (($unicode - ($unicode % 64)) / 64));
				$utf8 .= chr(128 + ($unicode % 64));
			} else {
				$utf8 .= chr(224 + (($unicode - ($unicode % 4096)) / 4096));
				$utf8 .= chr(128 + ((($unicode % 4096) - ($unicode % 64)) / 64));
				$utf8 .= chr(128 + ($unicode % 64));
			}
		}
		return $utf8;
	}
	
	/**
	 * callback函数，用于数组或对象加密
	 *
	 * @param mixed $name
	 * @param mixed $value
	 * @return string
	 */
	protected static function nameValue($name, $value, $charset) {
		return self::encode(strval($name), $charset) . ':' . self::encode($value, $charset);
	}
	
	/**
	 * utf8编码转为utf16BE
	 *
	 * @param string $string
	 * @param boolean $bom 是否Big-Endian
	 */
	protected static function utf8ToUTF16BE(&$string, $bom = false) {
		$out = $bom ? "\xFE\xFF" : '';
		if (function_exists('mb_convert_encoding')) {
			return $out . mb_convert_encoding($string, 'UTF-16BE', 'UTF-8');
		}
		
		$unicode = $values = array();
		$lookingFor = 1;
		for ($i = 0, $length = strlen($string); $i < $length; $i++) {
			$thisValue = ord($string[$i]);
			if ($thisValue < 128) {
				$unicode[] = $thisValue;
			} else {
				if (count($values) == 0) {
					$lookingFor = ($thisValue < 224) ? 2 : 3;
				}
				$values[] = $thisValue;
				if (count($values) == $lookingFor) {
					$unicode[] = ($lookingFor == 3) ? ($values[0] % 16) * 4096 + ($values[1] % 64) * 64 + $values[2] % 64 : ($values[0] % 32) * 64 + $values[1] % 64;
					$values = array();
					$lookingFor = 1;
				}
			}
		}
		
		foreach ($unicode as $cp) {
			$out .= pack('n', $cp);
		}
		return $out;
	}
	
}

?>