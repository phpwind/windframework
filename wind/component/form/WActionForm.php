<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
W::import('WIND:utilities.container.WModule');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WActionForm extends WModule {
	protected $_isValidate = false;
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function __construct($request, $response) {
		$this->_setProperties($request);
	}
	
	/**
	 * 验证方法，调用该方法完成所有验证操作
	 * 执行在继承WActionForm类的actionForm中，所有以validate结尾的函数
	 */
	public function validation() {
		$object = new ReflectionClass(get_class($this));
		$validationMethods = $object->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($validationMethods as $_value) {
			if (strtolower(substr($_value->name, -8)) == 'validate')
				call_user_func(array($this, $_value->name));
		}
	}
	
	private function _addError() {
		
	}
	
	/**
	 * 设置属性值
	 * 在继承WActionForm类的actionForm中，所有需要设置的属性应该显示的声明其setter函数用来进行属性设置
	 * @param WHttpRequest $request
	 */
	private function _setProperties($request) {
	   $_params = array();
	   if ($request->isGet()) $_params = $request->getGet();
	   elseif ($request->isPost()) $_params = $request->getPost();
	   if (!$_params) return false;
	   foreach ($_params as $_key => $_value) {
	   	  //是否设置了setter方法，表单中的空间名和form中的属性名一一对应
	   	   $this->$_key = $_value;
	   }
	}
	
	/**
	 * 是否开启验证
	 * @return string
	 */
	public function getIsValidation() {
		return $this->_isValidate;
	}

}