<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:core.filter.base.WindFilter");

/**
 * form组件的一个插件引入实现
 * 用户需要配置form组件的时候，只要在配置文件中<filters>的配置项中配置该formfilter即可自动使用用户定义的form
 * 配置文件中的配置如下：
 * <filters>
 * <filter name="WindFormFilter">
 * <filterName>WindFormFilter</filterName> 
 * <filterPath>WIND:component.form.WindFormFilter</filterPath> 
 * </filter>
 * </filters>
 *
 *form组件，将会对从请求变量中，属于用户设置的formName对应的form类中设置的变量进行一个赋值。
 *如果用户也定义了相关的验证操作，则也会执行验证操作。
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindFormFilter extends WindFilter {
	/**
	 * 接受用户需要使用form的名字，提供给用户的设置识别字串
	 * @var string
	 */
	const FORMNAME = 'formName';
	
	public function doAfterProcess($request, $response) {}
	/**
	 * 执行前置操作
	 * 
	 * 在执行用户action前进行执行用户的form操作（创建form, 给form赋值，执行form的验证，保存该form对象）
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function doBeforeProcess($request, $response) {
		$formObject = $this->getFormHandle($request, $response);
		if ($formObject === null) return;
		$formObject->setProperties($request->getGet());
		$formObject->setProperties($request->getPost());
		$this->validation($formObject);
	}
	
	/**
	 * 执行用户定义form中的验证操作
	 * 如果有错误信息则发送错误信息
	 * @param WindActionForm $formObject
	 */
	private function validation($formObject) {
		if (!$formObject->getIsValidation()) return false;
		$formObject->validation();
		$formObject->sendError();
	}
	
	/**
	 * 获得对应form的句柄
	 * 根据用户定义的formName来创建form,
	 * 定义用户定义的form
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function getFormHandle($request, $response) {
		$formName = $request->getGet(self::FORMNAME) ? $request->getGet(self::FORMNAME) : $request->getPost(self::FORMNAME);
		if (!$formName) return null;
		$module = C::getConfig('modules', $response->getDispatcher()->module);
		L::import($module['path'] . ".actionForm." . $formName);
		if (!class_exists($formName)) {
			throw new WindException('Class \'' . $formName . '\' is not exists!');
		}
		$formObject = L::getInstance($formName);
		if (!$formObject instanceof WindActionForm) {
			throw new WindException('The class \'' . $formName . '\' must extend WindActionForm!');
		}
		return $formObject;
	}
}