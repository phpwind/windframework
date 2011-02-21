<?php
/**
 * 工具库
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindUtility {

	/**
	 * 获得随机数字符串
	 * 
	 * @param int $length
	 * @return string
	 */
	static function generateRandStr($length) {
		$randstr = "";
		for ($i = 0; $i < (int) $length; $i++) {
			$randnum = rand(0, 61);
			if ($randnum < 10) {
				$randstr .= chr($randnum + 48);
			} else if ($randnum < 36) {
				$randstr .= chr($randnum + 55);
			} else {
				$randstr .= chr($randnum + 61);
			}
		}
		return $randstr;
	}

}

?>