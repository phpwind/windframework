<?php
/**
 * 启用了自动验证器的WindModule基类
 * 注入：验证器/异常处理器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindEnableValidateModule extends WindModule {
	protected $_validatorClass = 'WIND:utility.WindValidator';
	protected $errorController = '';
	protected $errorAction = '';
	private $_validator = null;
	private $_errors = array();
	private $_defaultMessage = 'the field validate fail.';

	/**
	 * @return the $_errors
	 */
	public function getErrors() {
		return $this->_errors;
	}

	public function getErrorControllerAndAction() {
		return array($this->errorController, $this->errorAction);
	}

	/**
	 * 返回验证规则
	 * 
	 * validator : required/not-required
	 * @return multitype:multitype:string  
	 */
	protected function validateRules() {
		return array();
	}

	/**
	 * 验证方法
	 * 
	 * @param array|WindModule $input
	 */
	public function validate(&$input) {
		if (is_array($input))
			$this->validateArray($input);
		elseif (is_object($input))
			$this->validateObject($input);
	}

	/**
	 * 验证数组类型的输入
	 * @param array $input
	 */
	private function validateArray(&$input) {
		$rules = $this->validateRules();
		foreach ((array) $rules as $rule) {
			$_input = isset($input[$rule['field']]) ? $input[$rule['field']] : '';
			$arg = (array) $rule['args'];
			array_unshift($arg, $_input);
			if (call_user_func_array(array($this->getValidator(), $rule['validator']), $arg) !== false) continue;
			if ($rule['default'] === null) {
				$this->_errors[$rule['field']] = $rule['message'];
				continue;
			}
			$input[$rule['field']] = $rule['default'];
		}
	}

	/**
	 * 验证对象类型的输入
	 * 需要设置set和get方式
	 * 
	 * @param object $input 传入需要验证的数据
	 * 
	 */
	private function validateObject(&$input) {
		$rules = $this->validateRules();
		$methods = get_class_methods($input);
		foreach ((array) $rules as $rule) {
			$getMethod = 'get' . ucfirst($rule['field']);
			$_input = in_array($getMethod, $methods) ? call_user_func(array($input, $getMethod)) : '';
			$arg = (array) $rule['args'];
			array_unshift($arg, $_input);
			if (call_user_func_array(array($this->getValidator(), $rule['validator']), $arg) !== false) continue;
			if ($rule['default'] === null) {
				$this->_errors[$rule['field']] = $rule['message'];
				continue;
			}
			$setMethod = 'set' . ucfirst($rule['field']);
			in_array($setMethod, $methods) && call_user_func_array(array($input, $setMethod), 
				array($rule['default']));
		}
	}

	/**
	 * @param WindValidator $validator
	 */
	protected function setValidator($validator) {
		$this->_validator = $validator;
	}

	/**
	 * 返回验证器
	 * @return WindValidator 
	 */
	protected function getValidator() {
		if ($this->_validator === null) {
			$_className = Wind::import($this->_validatorClass);
			$this->_validator = WindFactory::createInstance($_className);
			if ($this->_validator === null) throw new WindException('validator', WindException::ERROR_RETURN_TYPE_ERROR);
		}
		return $this->_validator;
	}
}