<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * json解码
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author	 Michal Migurski <mike-json@teczno.com>
 * @author	 Matt Knapp <mdknapp[at]gmail[dot]com>
 * @author	 Brett Stimmerman <brettstimmerman[at]gmail[dot]com>
 * @version $Id$ 
 * @package 
 * tags
 */
class WindDecoder {
	const JSON_SLICE = 1;
	const JSON_IN_STR = 2;
	const JSON_IN_ARR = 4;
	const JSON_IN_OBJ = 8;
	const JSON_IN_CMT = 16;
	public static function decode($str, $useArray = true) {
		$str = strtolower(self::reduceString($str));
		if ('true' == $str) {
			return true;
		} elseif ('false' == $str) {
			return false;
		} elseif ('null' == $str) {
			return null;
		} elseif (is_numeric($str)) {
			return (float)$str == (integer)$str ? (integer) $str : (float) $str;
		}elseif(preg_match('/^("|\').+(\1)$/s', $str, $matche) && $matche[1] == $matche[2]){
			return self::jsonToString($str);
		}elseif(preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)){
			return $useArray ? self::jsonToArray($str) : self::jsonToObject($str);
		}
		return false;
	}
	protected static function jsonToString($string) {
		$delim = substr($string, 0, 1);
		$chrs = substr($string, 1, -1);
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
		return $decodeStr;
	}
	protected static function jsonToArray($str) {
		return self::complexConvert($str,true);
	}
	protected static function jsonToObject($str) {
		return self::complexConvert($str,false);
	}
	protected static function complexConvert($str,$useArray = true){
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
					array_push($arr, self::decode($slice, $useArray));
				} elseif (reset($stk) == self::JSON_IN_OBJ) {
					if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
						$key = self::decode($parts[1], $useArray);
						$useArray ? $obj[$key] = self::decode($parts[2], $useArray) : $obj->$key = self::decode($parts[2], $useArray);
					} elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
						$useArray ? $obj[$parts[1]] = self::decode($parts[2], $useArray) : $obj->$parts[1] = self::decode($parts[2], $useArray);
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
	
	protected static function unicodeToUTF8(&$str) {
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
	
	protected static function reduceString($str) {
		return trim(preg_replace(array(
		'#^\s*//(.+)$#m', 
		'#^\s*/\*(.+)\*/#Us', 
		'#/\*(.+)\*/\s*$#Us'), 
		'', $str));
	}
	
	protected static function utf16beToUTF8(&$str) {
		return self::unicodeToUTF8(unpack('n*', $str));
	}

}