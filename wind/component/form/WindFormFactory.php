<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.factory.base.WindFactory');
L::import('WIND:component.request.base.IWindRequest');

/**
 * 设置form工具
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindFormFactory extends WindFactory {
	private $formObject = null;
	const FORMHANDLE = 'FormHandle';
    
	public function init($request) {
		if (!$request instanceof IWindRequest) return false;
		$_params = array();
	    if ($request->isGet()) $_params = $request->getGet();
	    elseif ($request->isPost()) $_params = $request->getPost();
	    if (!isset($_params[WindFormFactory::FORMHANDLE]) || $_params[WindFormFactory::FORMHANDLE] == '') 
	   		return false;
	    $formHandle = $_params[WindFormFactory::FORMHANDLE] . 'Form';
		L::import("actionControllers.actionForm.userForm");//////
	    if (!class_exists($formHandle)) return false;
	    $formObject = new $formHandle();
	    if (!$formObject instanceof WindActionForm) return false;
	    $formObject->setProperties($_params);
	    ($formObject->getIsValidation()) &&  $formObject->validation();
	    $this->formObject = $formObject;
	}
	
	public function getFormHandle() {
		return $this->formObject;
	}
	
	/**
	 * 创建一个工厂
	 * 
	 * @return WindFilterFactory
	 */
	public function getInstance() {
		return parent::getFactory(__CLASS__);
	}
}