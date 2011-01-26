<?php

L::import('WIND:core.filter.WindHandlerInterceptor');
L::import('WIND:component.log.WindLogger');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindLoggerListener extends WindHandlerInterceptor {

	private $logger;

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		WindLogger::info($this->getLogMessage());
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		WindLogger::info($this->getLogMessage());
		
	}

	/**
	 * Enter description here ...
	 */
	private function getLogMessage() {
		//TODO 当前执行的类和方法，需要记录输入输出
		$info = '';
		return $info;
	}
}

?>