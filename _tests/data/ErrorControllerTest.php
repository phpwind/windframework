<?php
class ErrorControllerTest extends WindErrorHandler {
	public function run(){
		parent::run();
		throw new Exception("error handled");
	}
}

?>