<?php

/**
 * 通用工具库
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

	/**
	 * 通用组装测试验证规则
	 * 
	 * @param string $field	| 验证字段名称
	 * @param string $validator | 验证方法
	 * @param array $args       | 参数
	 * @param string $default	| 默认值
	 * @param string $message	| 错误信息
	 * @return array
	 */
	static public function buildValidateRule($field, $validator, $args = array(), $default = null, $message = '') {
		return array('field' => $field, 'validator' => $validator, 'args' => (array) $args, 'default' => $default, 
			'message' => ($message ? $message : '提示：\'' . $field . '\'验证失败'));
	}
}

?>