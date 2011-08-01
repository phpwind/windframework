<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindHtmlHelper {

	/**
	 * Convert special characters to HTML entities
	 * 
	 * @param string $text | 
	 * @return string | string The converted string
	 */
	public static function encode($text) {
		return htmlspecialchars($text, ENT_QUOTES, Wind::getApp()->getWindSystemConfig()->getCharset());
	}

	/**
	 * Convert special characters to HTML entities
	 * 
	 * @param array $data
	 * @return array
	 */
	public static function encodeArray($data) {
		$_tmp = array();
		foreach ($data as $key => $value) {
			if (is_string($key)) $key = htmlspecialchars($key, ENT_QUOTES, 
				Wind::getApp()->getWindSystemConfig()->getCharset());
			if (is_string($value))
				$value = htmlspecialchars($value, ENT_QUOTES, Wind::getApp()->getWindSystemConfig()->getCharset());
			elseif (is_array($value))
				$value = self::encodeArray($value);
			$_tmp[$key] = $value;
		}
		return $_tmp;
	}

}

?>