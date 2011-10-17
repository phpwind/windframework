<?php
class LongController extends WindController {
	
	public $shi;
	public $long;
	
	public function run(){
		parent::run();
	}
	
	public function testAction(){
		
	}
	
	private function privateMethodAction(){
		
	}
	
	public function aaaAction(){
		throw new Exception("Long-aaa is running");
	}
	
	public function shiAction(){
		throw new WindException('exceptionThrowByLongController',404);
	}
	
	public function getRequest(){
		return parent::getRequest();
	}
	
	public function getResponse(){
		return parent::getResponse();
	}
	
	public function getInput($name, $type = '', $callback = null){
		return parent::getInput($name, $type, $callback);
	}
	
	public function forwardAction($action, $args, $isRedirect, $immediately){
		parent::forwardAction($action, $args, $isRedirect, $immediately);
	}
	
	public function forwardRedirect($url){
		parent::forwardRedirect($url);
	}
	
	public function setOutput($data, $key = ''){
		parent::setOutput($data, $key);
	}
	
	public function setGlobal($data, $key = ''){
		parent::setGlobal($data, $key);
	}
	
	public function showMessage($message = '', $key = '', $errorAction = ''){
		parent::showMessage($message, $key, $errorAction);
	}
	
	public function setTemplate($template) {
		parent::setTemplate($template);
	}
	
	public function setTemplatePath($templatePath) {
		parent::setTemplatePath($templatePath);
	}
	
	public function setTemplateExt($templateExt) {
		parent::setTemplateExt($templateExt);
	}
	
	public function setTheme($theme) {
		parent::setTheme($theme);
	}
	
	public function setLayout($layout) {
		parent::setLayout($layout);
	}
	
	public function resolveActionFilter($filters){
		parent::resolveActionFilter($filters);
	}
	
}