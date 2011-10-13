<?php
require_once 'filter/WindHandlerInterceptor.php';
class listener1 extends WindHandlerInterceptor{
	public $a;
	public function preHandle($a = ''){
		$this->a = $a . 'listener1_pre';
	}
	public function postHandle($a = ''){
		$this->a .= $a . '_post';
	}
}

class listener2 extends WindHandlerInterceptor{
	public $a;
	public $b;
	public function preHandle($a = '', $b = ''){
		$this->a = $a . 'listener2_pre';
		$this->b = $b . 'listener2_pre';
	}
	public function postHandle($a = '',$b = ''){
		$this->a .= $a . '_post';
		$this->b .= $b . '_post';
	}
}