<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindDecoder {
	
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
		}elseif(preg_match('/^("|\').+(\1)$/s', $str, $m) && $m[1] == $m[2]){
			return self::jsonToString($str);
		}
		switch (strtolower($str)) {
			case 'true':
				return true;
			case 'false':
				return false;
			case 'null':
				return null;
			default:
				if (is_numeric($str)) {
				} elseif (true) {
					
				} elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
					// array, or object notation
					if ($str{0} == '[') {
						$stk = array(self::JSON_IN_ARR);
						$arr = array();
					} else {
						if ($useArray) {
							$stk = array(self::JSON_IN_OBJ);
							$obj = array();
						} else {
							$stk = array(self::JSON_IN_OBJ);
							$obj = new stdClass();
						}
					}
					
					array_push($stk, array('what' => self::JSON_SLICE, 'where' => 0, 'delim' => false));
					
					$chrs = substr($str, 1, -1);
					$chrs = self::reduceString($chrs);
					
					if ($chrs == '') {
						if (reset($stk) == self::JSON_IN_ARR) {
							return $arr;
						
						} else {
							return $obj;
						
						}
					}
					
					//print("\nparsing {$chrs}\n");
					

					$strlen_chrs = strlen($chrs);
					
					for ($c = 0; $c <= $strlen_chrs; ++$c) {
						
						$top = end($stk);
						$substr_chrs_c_2 = substr($chrs, $c, 2);
						
						if (($c == $strlen_chrs) || (($chrs{$c} == ',') && ($top['what'] == self::JSON_SLICE))) {
							// found a comma that is not inside a string, array, etc.,
							// OR we've reached the end of the character list
							$slice = substr($chrs, $top['where'], ($c - $top['where']));
							array_push($stk, array('what' => self::JSON_SLICE, 'where' => ($c + 1), 
								'delim' => false));
							//print("Found split at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
							

							if (reset($stk) == self::JSON_IN_ARR) {
								// we are in an array, so just push an element onto the stack
								array_push($arr, self::decode($slice, $useArray));
							
							} elseif (reset($stk) == self::JSON_IN_OBJ) {
								// we are in an object, so figure
								// out the property name and set an
								// element in an associative array,
								// for now
								if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									// "name":value pair
									$key = self::decode($parts[1], $useArray);
									$val = self::decode($parts[2], $useArray);
									
									if ($useArray) {
										$obj[$key] = $val;
									} else {
										$obj->$key = $val;
									}
								} elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									// name:value pair, where name is unquoted
									$key = $parts[1];
									$val = self::decode($parts[2], $useArray);
									
									if ($useArray) {
										$obj[$key] = $val;
									} else {
										$obj->$key = $val;
									}
								}
							
							}
						
						} elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != self::JSON_IN_STR)) {
							// found a quote, and we are not inside a string
							array_push($stk, array('what' => self::JSON_IN_STR, 
								'where' => $c, 'delim' => $chrs{$c}));
							//print("Found start of string at {$c}\n");
						

						} elseif (($chrs{$c} == $top['delim']) && ($top['what'] == self::JSON_IN_STR) && (($chrs{$c - 1} != "\\") || ($chrs{$c - 1} == "\\" && $chrs{$c - 2} == "\\"))) {
							// found a quote, we're in a string, and it's not escaped
							array_pop($stk);
							//print("Found end of string at {$c}: ".substr($chrs, $top['where'], (1 + 1 + $c - $top['where']))."\n");
						

						} elseif (($chrs{$c} == '[') && in_array($top['what'], array(self::JSON_SLICE, 
							self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
							// found a left-bracket, and we are in an array, object, or slice
							array_push($stk, array('what' => self::JSON_IN_ARR, 
								'where' => $c, 'delim' => false));
							//print("Found start of array at {$c}\n");
						

						} elseif (($chrs{$c} == ']') && ($top['what'] == self::JSON_IN_ARR)) {
							// found a right-bracket, and we're in an array
							array_pop($stk);
							//print("Found end of array at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
						

						} elseif (($chrs{$c} == '{') && in_array($top['what'], array(self::JSON_SLICE, 
							self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
							// found a left-brace, and we are in an array, object, or slice
							array_push($stk, array('what' => self::JSON_IN_OBJ, 
								'where' => $c, 'delim' => false));
							//print("Found start of object at {$c}\n");
						

						} elseif (($chrs{$c} == '}') && ($top['what'] == self::JSON_IN_OBJ)) {
							// found a right-brace, and we're in an object
							array_pop($stk);
							//print("Found end of object at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
						

						} elseif (($substr_chrs_c_2 == '/*') && in_array($top['what'], array(self::JSON_SLICE, 
							self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
							// found a comment start, and we are in an array, object, or slice
							array_push($stk, array('what' => self::JSON_IN_CMT, 
								'where' => $c, 'delim' => false));
							$c++;
							//print("Found start of comment at {$c}\n");
						

						} elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == self::JSON_IN_CMT)) {
							// found a comment end, and we're in one now
							array_pop($stk);
							$c++;
							
							for ($i = $top['where']; $i <= $c; ++$i)
								$chrs = substr_replace($chrs, ' ', $i, 1);
							
						//print("Found end of comment at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
						

						}
					
					}
					
					if (reset($stk) == self::JSON_IN_ARR) {
						return $arr;
					
					} elseif (reset($stk) == self::JSON_IN_OBJ) {
						return $obj;
					
					}
				
				}
		}
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
	
	protected static function jsonToArray() {

	}
	
	protected static function jsonToObject() {

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
		$str = preg_replace(array(

		// eliminate single line comments in '// ...' form
		'#^\s*//(.+)$#m', 

		// eliminate multi-line comments in '/* ... */' form, at start of string
		'#^\s*/\*(.+)\*/#Us', 

		// eliminate multi-line comments in '/* ... */' form, at end of string
		'#/\*(.+)\*/\s*$#Us'), 

		'', $str);
		
		// eliminate extraneous space
		return trim($str);
	}
	
	protected static function utf16beToUTF8(&$str) {
		$uni = unpack('n*', $str);
		return self::unicodeToUTF8($uni);
	}

}