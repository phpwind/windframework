<?php
class LongController extends WindController {
	
	public $shi;
	public $long;
	
	public function run(){
		echo 'LongController-run';
	}
	
	public function testAction(){
		echo 'LongController-test';
	}
	
	public function noPrintAction() {
		
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
		return parent::forwardAction($action, $args, $isRedirect, $immediately);
	}
	
	public function forwardRedirect($url){
		return parent::forwardRedirect($url);
	}
	
	public function setOutput($data, $key = ''){
		return parent::setOutput($data, $key);
	}
	
	public function setGlobal($data, $key = ''){
		return parent::setGlobal($data, $key);
	}
	
	public function showMessage($message = '', $key = '', $errorAction = ''){
		return parent::showMessage($message, $key, $errorAction);
	}
	
	public function setTemplate($template) {
		return parent::setTemplate($template);
	}
	
	public function setTemplatePath($templatePath) {
		return parent::setTemplatePath($templatePath);
	}
	
	public function setTemplateExt($templateExt) {
		return parent::setTemplateExt($templateExt);
	}
	
	public function setTheme($theme) {
		return parent::setTheme($theme);
	}
	
	public function setThemePackage($package) {
		return parent::setThemePackage($package);
	}
	
	public function setLayout($layout) {
		return parent::setLayout($layout);
	}
	
	public function resolveActionFilter($filters){
		return parent::resolveActionFilter($filters);
	}
	
}