<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.base.WindModule');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
abstract class WindActionForm extends WindModule {
	private $isValidation = true;
	private $error = array();
	private $errorAction = '';
	private $errorActionClass = '';
	
	/**
	 * 设置是否开启验证
	 * @param boolean $isValidate
	 */
	public function setIsValidation($isValidation) {
		$this->isValidation = (boolean)$isValidation;
	}
	
	/**
	 * 是否开启验证
	 * @return string
	 */
	public function getIsValidation() {
		return $this->isValidation;
	}
	
	/**
	 * 验证方法，调用该方法完成所有验证操作
	 * get_class_methods对于继承中的只返回public类型的函数
	 * 执行，用户的继承WindActionForm类的actionForm中，所有以validate结尾的函数
	 */
	public function validation() {
		$methods = get_class_methods($this);
		foreach ($methods as $value) {
			if ((strlen($value) != 8) && (strtolower(substr($value, -8)) == 'validate')) call_user_func(array($this, $value));
		}
	}
	
	/**
	 * 设置属性值
	 * @param array $_params
	 */
	public function setProperties($params) {
		if (!$params) return false;
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
		return true;
	}
	
	/**
	 * 添加验证中产生的错误信息
	 * @param string $message
	 */
	public function addError($message, $key = '') {
		if (!$message) return false;
		if (is_array($message)) {
			foreach ($message as $key => $value) {
				$this->error[$key] = $value;
			}
		} else {
			if ($key) {
				$this->error[$key] = $message;
			} else {
				$this->error[] = $message;
			}
		}
		return false;
	}
	
	/**
	 * 获得Error信息
	 * @return array 错误信息
	 */
	public function getError($key = '') {
		return ($key === '') ? $this->error : $this->error[trim($key)];
	}
	
	/**
	 * 设置错误处理操作
	 * @param string $path 错误处理类路径
	 * @param string $action 错误处理action，默认为run
	 */
	public function setErrorAction($path, $action = 'run') {
		$this->errorAction = $action;
		$this->errorActionClass = $path;
	}
	
	/**
	 * 返回错误处理操作的Action
	 * @return string ErrorAction
	 */
	public function getErrorAction() {
		return array($this->errorAction, $this->errorActionClass);
	}
}