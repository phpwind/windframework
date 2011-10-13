<?php

require_once ('filter/WindHandlerInterceptor.php');

class Listener extends WindHandlerInterceptor {
	public $name;
	/**
	 * @var WindForward
	 */
	public $forward;
	/**
	 * @var WindErrorMessage
	 */
	public $errorMessage;
	public function preHandle(){
		$this->forward->setAction('pre_' . $this->forward->getAction());
		$this->errorMessage->addError('pre_' . $this->errorMessage->getError(0), 0);
	}
	public function postHandle(){
		$this->forward->setAction('post_' . $this->forward->getAction());
		$this->errorMessage->addError('post_' . $this->errorMessage->getError(0), 0);
	}
	
	public function __construct($forward, $errorMessage){
		$this->forward = $forward;
		$this->errorMessage = $errorMessage;
	}
	
}

?>