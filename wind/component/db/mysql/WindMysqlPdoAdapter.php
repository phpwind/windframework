<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindMysqlPdoAdapter extends PDO {
    
	/**
	 * 设置链接使用字符集
	 * @param string $charset
	 */
	public function setCharset($charset) {
		if (!$charset) $charset = 'gbk';
		$this->query("set names " . $this->quote($charset) . ";");
	}

	/**
	 * 过滤数组变量，将数组变量转换为字符串，并用逗号分隔每个数组元素支持多维数组
	 * example：
	 * array('a','b','c') => ('a','b','c')
	 * array(array('a1','b1','c1'),array('a2','b2','c2')) 
	 * => ('a1','b1','c1'),('a2','b2','c2')
	 * @param array $variable
	 * @param string $result
	 */
	public function filterArray($variable, $result = '') {
		if (empty($variable) || !is_array($variable)) return;
		$_result = '';
		foreach ($variable as $key => $value) {
			if (is_array($value))
				$result = $this->filterArray($value, $result);
			else {
				$_result .= $this->quote($value) . ',';
			}
		}
		if ($_result) {
			$result .= $result ? ',(' . trim($_result, ',') . ')' : '(' . trim($_result, ',') . ')';
		}
		return $result;
	}
	
	/**
	 * 组装单条 key=value 形式的SQL查询语句值 insert/update
	 * @param $array
	 * @param $strip
	 * @return string
	 */
	public function sqlSingle($array) {
		if (!is_array($array)) return ''; 
		$str = '';
		foreach ($array as $key => $val) {
			$str .= ($str ? ', ' : ' ') . $this->fieldMeta($key) . '=' . $this->quote($val);
		}
		return $str;
	}
	
	/**
	 * 过滤SQL元数据，数据库对象(如表名字，字段等)
	 * @param $data 元数据
	 * @return string 经过转义的元数据字符串
	 */
	private function fieldMeta($data) {
		$data = str_replace(array('`', ' '), '',$data);
		return ' `'.$data.'` ';
	}
}
?>