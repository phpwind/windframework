<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

require_once 'core/web/WindHandlerInterceptor.php';

class Listener extends WindHandlerInterceptor {

	public $test;

	public $testB;

	public function preHandle($userName = '') {
		$this->test = $userName . '_preHandle';
	}

	public function postHandle($userName = '') {
		$this->testB = $userName . '_postHandle';
	}

}

class Listener1 extends WindHandlerInterceptor {

	public $arg1;

	public $arg2;

	public function preHandle($arg1 = '', $arg2 = '') {
		$this->arg1 = $arg1 . '_pre';
		$this->arg2 = $arg2 . '_pre';
	}

	public function postHandle($arg1 = '', $arg2 = '') {
		$this->arg1 .= '_' . $arg1 . '_post';
		$this->arg2 .= '_' . $arg2 . '_post';
	}

}

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class Persion {

	public $name = '';

	public $arg1;

	public $arg2;

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	public function testPersion($arg1, $arg2) {
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
		return $this->arg1;
	}

}