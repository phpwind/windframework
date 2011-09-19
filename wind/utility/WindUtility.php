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
	 * 解析表达式
	 * 表达式格式: namespace:arg1.arg2.arg3.arg4.arg5==value
	 * 返回: array($namespace,$param,$operator,$value)
	 * @param string $expression
	 * @return multitype:unknown Ambigous <string, multitype:> multitype: 
	 */
	public static function resolveExpression($expression) {
		$operators = array('==', '!=', '<', '>', '<=', '>=');
		$operatorsReplace = array('#==#', '#!=#', '#<#', '#>#', '#<=#', '#>=#');
		list($p, $o, $v2) = explode('#', str_replace($operators, $operatorsReplace, $expression));
		if (strpos($p, ":") !== false)
			list($_namespace, $p) = explode(':', trim($p, ':'));
		else
			$_namespace = '';
		return array($_namespace, $p, $o, $v2);
	}

	/**
	 * 执行简单的条件表达式
	 * @param string $v1
	 * @param string $v2
	 * @param string $expression
	 * @return
	 */
	public static function evalExpression($v1, $v2, $operator) {
		switch ($operator) {
			case '==':
				return $v1 == $v2;
			case '!=':
				return $v1 != $v2;
			case '<':
				return $v1 < $v2;
			case '>':
				return $v1 > $v2;
			case '<=':
				return $v1 <= $v2;
			case '>=':
				return $v1 >= $v2;
			default:
				return false;
		}
		return false;
	}

	/**
	 * 递归合并两个数组
	 * 
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	public static function mergeArray($array1, $array2) {
		foreach ($array2 as $key => $value) {
			if (!isset($array1[$key]) || !is_array($array1[$key])) {
				$array1[$key] = $value;
				continue;
			}
			$array1[$key] = self::mergeArray($array1[$key], $array2[$key]);
		}
		return $array1;
	}

	/**
	 * 将字符串首字母小写
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function lcfirst($str) {
		if (function_exists('lcfirst')) return lcfirst($str);
		
		$str[0] = strtolower($str[0]);
		return $str;
	}

	/**
	 * 获得随机数字符串
	 * 
	 * @param int $length
	 * @return string
	 */
	public static function generateRandStr($length) {
		$randstr = "";
		for ($i = 0; $i < (int) $length; $i++) {
			$randnum = mt_rand(0, 61);
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
	public static function buildValidateRule($field, $validator, $args = array(), $default = null, $message = '') {
		return array(
			'field' => $field, 
			'validator' => $validator, 
			'args' => (array) $args, 
			'default' => $default, 
			'message' => ($message ? $message : '提示：\'' . $field . '\'验证失败'));
	}

}

?>