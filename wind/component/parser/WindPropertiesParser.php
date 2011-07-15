<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * properties格式文件解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindPropertiesParser {
	
	const COMMENT = '#';
	const LPROCESS = '[';
	const RPROCESS = ']';
	private $separator = '.';
	
	public function __construct() {

	}
	
	/**
	 * 解析properties文件里的内容 
	 * @param string $filename 文件名
	 * @param boolean $process 是否处理指令
	 * @param boolean $build   是否按格式解析数据
	 * @return array
	 */
	public function parse($filename, $process = true, $build = true) {
		$data = $this->parse_properties_file($filename, $process);
		return $build ? $this->buildData($data) : $data;
	}
	
	/**
	 * 载入一个由 filename 指定的 properties 文件，
	 * 并将其中的设置作为一个联合数组返回。
	 * 如果将最后的 process参数设为 TRUE，
	 * 将得到一个多维数组，包括了配置文件中每一节的名称和设置。
	 * process_sections 的默认值是 true。 
	 * @param string $filename 文件名
	 * @param unknown_type $process 是否处理指令
	 * @return array
	 */
	public function parse_properties_file($filename, $process = true) {
		if (!is_file($filename) || !in_array(substr($filename, strrpos($filename, '.') + 1), array('properties'))) {
			return array();
		}
		$fp = fopen($filename, 'r');
		$content = fread($fp, filesize($filename));
		fclose($fp);
		$content = explode("\n", $content);
		$data = array();
		$last_process = $current_process = '';
		foreach ($content as $key => $value) {
			$value = str_replace(array("\n", "\r"), '', trim($value));
			if (0 === strpos(trim($value), self::COMMENT) || in_array(trim($value), array('', "\t", "\n"))) {
				continue;
			}
			$tmp = explode('=', $value);
			if (0 === strpos(trim($value), self::LPROCESS) && (strlen($value) - 1) === strrpos($value, self::RPROCESS)) {
				if ($process) {
					$current_process = $this->trimChar(trim($value), array(self::LPROCESS, self::RPROCESS));
					$data[$current_process] = array();
					$last_process = $current_process;
				}
				continue;
			}
			$tmp[0] = trim($tmp[0]);
			$tmp[1] = trim($tmp[1]);
			
			if ($last_process) {
				count($tmp) > 1 ? $data[$last_process][$tmp[0]] = $tmp[1] : $data[$last_process][$tmp[0]] = '';
			} else {
				count($tmp) > 1 ? $data[$tmp[0]] = $tmp[1] : $data[$tmp[0]] = '';
			}
		}
		return $data;
	
	}
	
	/**
	 * 解析数据
	 * @param array $data
	 * @return array
	 */
	public function buildData(&$data) {
		foreach ((array)$data as $key => $value) {
			if (is_array($value)) {
				$data[$key] = $this->formatDataArray($value);
			} else {
				$this->formatDataFromString($key, $value, $data);
			}
		}
		return $data;
	}
	
	/**
	 * 将proterties文件每行转换成数组
	 * @param string $key ini文件中的键
	 * @param string $value ini文件中的值
	 * @param array $data
	 * @return array
	 */
	public function toArray($key, $value, &$data = array()) {
		if (empty($key) && empty($value)) return array();
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
	 * 将原始数组合并成新的数组
	 * @param array $original 原始数组
	 * @param array $data 合并后的数组
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
					$this->formatDataArray($tValue, $data[$tkey]);
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
	public function formatDataFromString($key, $value, &$data) {
		$tmp = $this->toArray($key, $value);
		if(false == strpos($key, $this->separator)){
			return $tmp;
		}
		$start = substr($key, 0, strpos($key, $this->separator));
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
	
	/**
	 * 去除字符串头和尾中指定字符
	 * @param string $str
	 * @param mixed $char
	 * @return string
	 */
	private function trimChar($str, $char = ' ') {
		$char = is_array($char) ? $char : array($char);
		foreach ($char as $value) {
			$str = trim($str, $value);
		}
		return $str;
	}

}