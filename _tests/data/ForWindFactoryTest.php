<?php
class ForWindFactoryTest{
	
	public $param = 1;
	public $session;
	public $forward;
	public function clear(){
		$this->param = 0;
		throw new WindException("test");
	}
	
	public function ForWindFactoryTest($param = 1, $session = null, $forward = null){
		$this->param = $param;
		$this->session = $session;
		$this->forward = $forward;
	}
	
	public function setParam($param){
		$this->param = $param;
	}
	
	public function setSession($session){
		$this->session = $session;
	}
	
	public function init($session = null, $forward = null){
		$this->session = $session;
		$this->forward = $forward;
	}
}