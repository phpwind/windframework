<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindMessage {
	
	private $message = array();
	
	/**
	 * 添加一条message信息
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addMessage($message, $key = '') {
		if (empty($message)) return;
		if (is_array($message)) {
			foreach ($message as $key => $value) {
				$this->addMessage($value, $key);
			}
		} else {
			if ($key)
				$this->message[$key] = $message;
			else
				$this->message[] = $message;
		}
	}
	
	/**
	 * 获得一条message信息
	 * 
	 * @param string $key
	 * @return string|array
	 */
	public function getMessage($key = '') {
		return ($key !== '' && $key !== null) ? $this->message[$key] : $this->message;
	}
	
	/**
	 * 以数组的方式返回message信息
	 * 考虑为对象的情况，如果直接用(array)强制转换会出现问题
	 * 
	 * @param string $key
	 * @return array
	 */
	public function getMessageWithArray($key = '') {
		$args = $this->getMessage($key);
		return (is_array($args)) ? $args : array($args);
	}
	
	/**
	 * 以字符串格式返回message
	 * 如果含有object对象，则去除该对象
	 * @return string
	 */
	public function getMessageWithString($key = '') {
		$args = (array) $this->getMessage($key);
		foreach ($args as $key => $message) {
			if (is_object($message)) {
				$args[$key] = null;
				unset($args[$key]);
			}
		}
		return trim(implode(',', $args), ',');
	}
	
	/**
	 * 清理message
	 * 
	 * @param string $key
	 */
	public function clear($key = '') {
		if ($key !== '' && $key !== null)
			unset($this->message[$key]);
		else
			$this->message = array();
	}

}