<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindEncoder {
	
	/**
	 * @param mixed $var
	 * @return string
	 */
	public static function encode($value) {
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
				return self::stringToJson($value);
			case 'array':
				return self::arrayToJson($value);
			case 'object':
				return self::objectToJson($value);
			default:
				return '';
		}
		return '';
	}
	/**
	 * 将字符串转化成json格式对象
	 * @param string $string
	 * @return string
	 */
	protected static function stringToJson($string) {
		if ('UTF-8' !== ($enc = strtoupper(self::$charset))) {
			$string = iconv($enc, 'UTF-8', $string);
		}
		$ascii = '';
		$strlen = strlen($string);
		for ($c = 0; $c < $strlen; ++$c) {
			$ordVar = ord($string{$c});
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
				$ascii .= '\\' . $string{$c};
			} elseif (0x20 <= $ordVar && 0x7F >= $ordVar) {
				$ascii .= $string{$c}; //ASCII
			} elseif (0xC0 == ($ordVar & 0xE0)) {
				$char = pack('C*', $ordVar, ord($string{++$c}));
				$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
			} elseif (0xE0 == ($ordVar & 0xF0)) {
				$char = pack('C*', $ordVar, ord($string{++$c}), ord($string{++$c}));
				$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
			} elseif (0xF0 == ($ordVar & 0xF8)) {
				$char = pack('C*', $ordVar, ord($string{++$c}), ord($string{++$c}), ord($string{++$c}));
				$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
			} elseif (0xF8 == ($ordVar & 0xFC)) {
				$char = pack('C*', $ordVar, ord($string{++$c}), ord($string{++$c}), ord($string{++$c}), ord($string{++$c}));
				$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
			} elseif (0xFC == ($ordVar & 0xFE)) {
				$char = pack('C*', $ordVar, ord($string{++$c}), ord($string{++$c}), ord($string{++$c}), ord($string{++$c}), ord($string{++$c}));
				$ascii .= sprintf('\u%04s', bin2hex(self::utf8ToUTF16BE($char)));
			}
		}
		return '"' . $ascii . '"';
	}
	/**
	 * 将数组转化成json格式对象
	 * @param array $array
	 * @return string
	 */
	protected static function arrayToJson(array $array) {
		if (is_array($array) && count($array) && (array_keys($array) !== range(0, sizeof($array) - 1))) {
			return '{' . join(',', array_map(array('WindEncoder', 'nameValue'), array_keys($array), array_values($array))) . '}';
		}
		return '[' . join(',', array_map(array('WindEncoder', 'encode'), $array)) . ']';
	}
	/**
	 * 将对象转化成json格式对象
	 * @param string $object
	 * @return string
	 */
	protected static function objectToJson($object) {
		if ($object instanceof Traversable) {
			$vars = array();
			foreach ($object as $k => $v) {
				$vars[$k] = $v;
			}
		} else {
			$vars = get_object_vars($object);
		}
		return '{' . join(',', array_map(array('WindEncoder', 'nameValue'), array_keys($vars), array_values($vars))) . '}';
	}
	
	protected static function nameValue($name, $value) {
		return self::encode(strval($name)) . ':' . self::encode($value);
	}
	
	protected static function utf8ToUTF16BE(&$string, $bom = false) {
		$out = $bom ? "\xFE\xFF" : '';
		if (function_exists('mb_convert_encoding')) {
			return $out . mb_convert_encoding($string, 'UTF-16BE', 'UTF-8');
		}
		$uni = self::utf8ToUnicode($string);
		foreach ($uni as $cp) {
			$out .= pack('n', $cp);
		}
		return $out;
	}
	
	protected static function utf8ToUnicode(&$string) {
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
		return $unicode;
	}

}