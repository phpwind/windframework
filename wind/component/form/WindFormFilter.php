<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.filter.base.WindFilter");
L::import("WIND:component.form.WindFormFactory");

class WindFormFilter extends WindFilter {
	
	public function doBeforeProcess($request, $response) {
		WindFormFactory::getInstance()->init($request);
	}
	public function doAfterProcess($request, $response) {
		
	}
}