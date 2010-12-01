<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.filter.base.WindFilter");

class WindFormFilter extends WindFilter {
	const FORMHANDLE = 'FormHandle';
	
	private function setForm($request, $response) {
		if (!$request instanceof IWindRequest) return false;
		$_params = array();
	    if ($request->isGet()) $_params = $request->getGet();
	    elseif ($request->isPost()) $_params = $request->getPost();
	    if (!isset($_params[self::FORMHANDLE]) || $_params[self::FORMHANDLE] == '') 
	   		return false;
	    $formHandle = $_params[self::FORMHANDLE] . 'Form';
	    var_dump($response);
		L::import("actionControllers.actionForm.userForm");//////
	    if (!class_exists($formHandle)) return false;
	  //  $formObject = $formHandle::getInstance();
	    var_dump($formObject);
	    if (!$formObject instanceof WindActionForm) return false;
	    $formObject->setProperties($_params);
	    ($formObject->getIsValidation()) &&  $formObject->validation();
	    $this->formObject = $formObject;
	}
	
	public function doBeforeProcess($request, $response) {
		$this->setForm($request, $response);
	}
	public function doAfterProcess($request, $response) {
		
	}
}