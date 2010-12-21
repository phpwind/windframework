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
	private $isValidation = false;
	private $error = null;
	
	public function __construct() {
		$this->error = WindErrorMessage::getInstance();
	}
	
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
			if (strtolower(substr($value, -8)) == 'validate') call_user_func(array($this, $value));
		}
	}
	
	/**
	 * 添加验证中产生的错误信息
	 * @param string $message
	 */
	public function addError($message, $key = '') {
		$this->error->addError($message, $key);
		return false;
	}
	
	/**
	 * 设置错误处理操作
	 *
	 */
	public function setErrorAction($action = '') {
		$this->error->setErrorAction($action);
	}
	public function sendError() {
		$this->error->sendError();
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
}