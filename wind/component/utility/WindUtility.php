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
	 * 分页函数
	 * @param $page		当前页
	 * @param $total	总页数
	 * @param $url		连接地址
	 * @param $ajaxCallBack
	 * @return string
	 */
	static public function getPages($page, $total, $url, $ajaxCallBack = '') {
		if (1 >= $total || !is_numeric($page)) {
			return '';
		}
		$ajaxurl = $ajaxCallBack ? " onclick=\"return $ajaxCallBack(this.href);\"" : '';
		list($url, $mao) = explode('#', $url);
		$mao && $mao = '#' . $mao;
		$pages = "<div class=\"pages\"><a href=\"{$url}page=1$mao\"{$ajaxurl}>&laquo;</a>";
		for ($i = $page - 3; $i <= $page - 1; $i++) {
			if ($i < 1) continue;
			$pages .= "<a href=\"{$url}page=$i$mao\"{$ajaxurl}>$i</a>";
		}
		$pages .= "<b>$page</b>";
		if ($page < $total) {
			$flag = 0;
			for ($i = $page + 1; $i <= $total; $i++) {
				$pages .= "<a href=\"{$url}page=$i$mao\"{$ajaxurl}>$i</a>";
				$flag++;
				if ($flag == 4) break;
			}
		}
		$pages .= "<a href=\"{$url}page=$total$mao\"{$ajaxurl}>&raquo;</a><div class=\"fl\">共{$total}页</div><span class=\"pagesone\"><input type=\"text\" size=\"3\" onkeydown=\"javascript: if(event.keyCode==13){var page=(this.value>$total) ? $total : this.value; " . ($ajaxurl ? "$ajaxCallBack('{$url}page='+page);" : " location='{$url}page='+page+'{$mao}';") . " return false;}\"><button onclick=\"javascript: var page=(this.previousSibling.value>$total) ? $total : this.previousSibling.value; " . ($ajaxurl ? "$ajaxCallBack('{$url}page='+page);" : " location='{$url}page='+page+'{$mao}';") . " return false;\">Go</button></span></div>";
		return $pages;
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