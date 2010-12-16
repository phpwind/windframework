<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ini 格式文件解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindIniParser {
	
	/**
	 * @var string 分割数组标识
	 */
	protected $separator = '.';
	
	/**
	 * 解析数据
	 * @param string $filename ini格式文件
	 * @param boolean $process  处理指令
	 * @param boolean $build 是否按指定格式返回
	 * @return boolean
	 */
	public function parse($filename, $process = true, $build = true) {
		if (!is_file($filename)) {
			return array();
		}
		$data = parse_ini_file($filename, $process);
		return $build ? $this->buildData($data) : $data;
	}
	
	/**
	 * 构建数据
	 * @param array $data
	 * @return array
	 */
	public function buildData(&$data) {
		foreach ((array)$data as $key => $value) {
			if (is_array($value)) {
				$data[$key] = $this->formatDataArray($value);
			} else {
				$this->fromatDataFromString($key, $value, $data);
			}
		}
		return $data;
	}
	
	/**
	 * 将每行ini文件转换成数组
	 * @param string $key ini文件中的键
	 * @param string $value ini文件中的值
	 * @param array $data
	 * @return array
	 */
	public function toArray($key, $value, &$data = array()) {
		if (strpos($key, $this->separator)) {
			$start = substr($key, 0, strpos($key, $this->separator));
			$end = substr($key, strpos($key, $this->separator) + 1);
			$data[$start] = array();
			$this->toArray($end, $value, $data[$start]);
		} else {
			$data[$key] = $value;
		}
		return $data;
	}
	
	/**
	 * 解析ini格式文件成数组
	 * @param array $original 原始数组
	 * @param array $data 解析后的数组
	 * @return array
	 */
	public function formatDataArray(&$original, &$data = array()) {
		foreach ((array)$original as $key => $value) {
			$tmp = $this->toArray($key, $value);
			foreach ($tmp as $tkey => $tValue) {
				if (is_array($tValue)) {
					if (!isset($data[$tkey])) {
						$data[$tkey] = array();
					}
					$this->formatDataArray($tValue, &$data[$tkey]);
				} else {
					$data[$tkey] = $tValue;
				}
			}
		}
		return $data;
	}
	
	/**
	 * 从字符串中合并数组
	 * @param string $key
	 * @param  string $value
	 * @param array $data
	 * return array
	 */
	public function fromatDataFromString($key, $value, &$data) {
		$start = substr($key, 0, strpos($key, $this->separator));
		$tmp = $this->toArray($key, $value);
		if ((!isset($data[$start]) || !is_array($data[$start])) && isset($tmp[$start])) {
			$data[$start] = $tmp[$start];
		} else {
			foreach ($data as $d_key => $d_value) {
				if (!isset($tmp[$d_key]) || !is_array($tmp[$d_key])) {
					continue;
				}
				foreach ($tmp[$d_key] as $a => $b) {
					$this->merge($a, $b, $data[$start]);
				}
			}
		}
		unset($data[$key]);
		return $data;
	}
	
	/**
	 * 合并格式化的数组
	 * @param string $key
	 * @param mixed $value
	 * @param array $data
	 * @return array
	 */
	private function merge($key, $value, &$data = array()) {
		if (is_array($value)) {
			$v_key = array_keys($value);
			$c_key = $v_key[0];
			if (is_array($value[$c_key])) {
				$this->merge($c_key, $value[$c_key], $data[$key]);
			} else {
				$data[$key][$c_key] = $value[$c_key];
			}
		} else {
			$data[$key] = $value;
		}
		return $data;
	}
}