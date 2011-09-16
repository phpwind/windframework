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
	 * @param string $str
	 * @return string
	 */
	public static function escapeHTML($str) {
		if (is_array($str)) {
			$_tmp = array();
			foreach ($str as $key => $value) {
				is_string($key) && $key = self::escapeHTML($str);
				$_tmp[$key] = self::escapeHTML($value);
			}
			return $_tmp;
		}
		return htmlspecialchars($str, ENT_QUOTES, Wind::getApp()->getResponse()->getCharset());
	}

	/**
	 * 路径转换
	 * @param $fileName
	 * @param $ifCheck
	 * @return string
	 */
	public static function escapePath($filePath, $ifCheck = false) {
		$_tmp = array("'" => '', '#' => '', '=' => '', '`' => '', '$' => '', '%' => '', '&' => '', ';' => '');
		$_tmp['://'] = $_tmp["\0"] = '';
		$ifCheck && $_tmp['..'] = '';
		if (strtr($filePath, $_tmp) == $filePath) return preg_replace('/[\/\\\]{1,}/i', '/', $filePath);
		if (WIND_DEBUG & 2) {
			Wind::getApp()->getComponent('windLogger')->info("[utility.WindSecurity.escapePath] file path is illegal.\r\n\tFilePath:" . $filePath);
		}
		throw new WindException('[utility.WindSecurity.escapePath] file path is illegal');
	}

}