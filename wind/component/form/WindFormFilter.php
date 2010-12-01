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

	public function doBeforeProcess($request, $response) {
		if (!$request instanceof IWindRequest) return false;
		$_params = array();
	    if ($request->isGet()) $_params = $request->getGet();
	    elseif ($request->isPost()) $_params = $request->getPost();
	    if (!isset($_params[self::FORMHANDLE]) || $_params[self::FORMHANDLE] == '') 
	   		return false;
	    $formHandle = $_params[self::FORMHANDLE];
	    $module = C::getConfig('modules', $response->getDispatcher()->getModule());
		L::import($module['path'] . ".actionForm." . $formHandle);
	    if (!class_exists($formHandle)) return false;
	    /**************************************************/
	    $formObject = new $formHandle();
	    if (!$formObject instanceof WindActionForm) return false;
	    $formObject->setProperties($_params);
	    ($formObject->getIsValidation()) &&  $formObject->validation();
	    $formObject->setInstance($formObject);
	}
	
	public function doAfterProcess($request, $response) {
		
	}
}