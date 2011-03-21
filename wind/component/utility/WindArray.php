<?php
/**
 *@author Su Qian <weihu@alibaba-inc.com> 2010-11-7
 *@link http://www.phpwind.com
 *@copyright Copyright &copy; 2003-2110 phpwind.com
 *@license 
 */

/**
 * 数组工具类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
class WindArray {

	/**
	 * 按指定key合并两个数组
	 * @param string key    合并数组的参照值
	 * @param array $array1  要合并数组
	 * @param array $array2  要合并数组
	 * @return array 返回合并的数组
	 */
	public static function mergeArrayWithKey($key, array $array1, array $array2) {
		if (!$key || !$array1 || !$array2) {
			return array();
		}
		$array1 = self::rebuildArrayWithKey($key, $array1);
		$array2 = self::rebuildArrayWithKey($key, $array2);
		$tmp = array();
		foreach ($array1 as $key => $array) {
			if (isset($array2[$key])) {
				$tmp[$key] = array_merge($array, $array2[$key]);
				unset($array2[$key]);
			} else {
				$tmp[$key] = $array;
			}
		}
		return array_merge($tmp, (array) $array2);
	}

	/**
	 * 按指定KEY重新生成数组
	 * @param string key 	重新生成数组的参照值
	 * @param array  $array 要重新生成的数组
	 * @return array 返回重新生成后的数组
	 */
	public static function rebuildArrayWithKey($key, array $array) {
		if (!$key || !$array) {
			return array();
		}
		$tmp = array();
		foreach ($array as $_array) {
			if (isset($_array[$key])) {
				$tmp[$_array[$key]] = $_array;
			}
		}
		return $tmp;
	}
}