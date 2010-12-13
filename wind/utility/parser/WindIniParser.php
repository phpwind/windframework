<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * php.ini 格式文件解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindIniParser {
	private $data = '';
	
	public function __construct() {
	
	}
	public function parse($filename, $process = true) {
		if (empty ( $filename )) {
			return false;
		}
		$data = parse_ini_file ( $filename, $process );
		$this->data = $this->buildData($data);
		return true;
	}
	
	public function buildData(&$data){
		foreach($data as $key=>$value){
			if(is_array($value)){
				$data[$key] = $this->formatData($value);
			}else{
				$data[$key] = $value;
			}
		}
		return $data;
	}
	
	public function toArray($key, $value, &$data = array()) {
		if (strpos ( $key, '.' )) {
			$start = substr ( $key, 0, strpos ( $key, '.' ) );
			$end = substr ( $key, strpos ( $key, '.' ) + 1 );
			$data [$start] = array ();
			$this->toArray ( $end, $value, $data [$start] );
		} else {
			$data [$key] = $value;
		}
		return $data;
	}
	
	public function formatData(&$original, &$data = array()) {
		foreach ( $original as $key => $value ) {
			$tmp = $this->toArray ( $key, $value );
			foreach ( $tmp as $tkey => $tValue ) {
				if (is_array ( $tValue )) {
					if (!isset ( $data [$tkey] )) {
						$data [$tkey] = array ();
					}
					$this->formatData ( $tValue, &$data [$tkey] );
				} else {
					$data [$tkey] = $tValue;
				}
			}
		}
		return $data;
	}
	
	public function getData() {
		return $this->data;
	}

}