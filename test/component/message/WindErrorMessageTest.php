<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
require_once(WIND_PATH . '/component/message/WindMessage.php');

class WindErrorMessageTest extends BaseTestCase {
	public function setUp() {
	}
	public function tearDown() {
		
	}
	public function testGetInstance() {
		$obj = WindErrorMessage::getInstance();
		$this->assertTrue(is_object($obj) && $obj instanceof WindErrorMessage);
	}
	
	public function testGetAndAddError() {
		$obj = WindErrorMessage::getInstance();
		$obj->addError('nameError', 'name');
		$this->assertEquals('nameError', $obj->getError('name'));
		$obj->addError('', 'password');
		$this->assertEquals('', $obj->getError('password'));
		$obj->addError(array('false', 'true'), 'flag');
		$this->assertEquals('false', $obj->getError(0));
		$this->assertEquals('true', $obj->getError(1));
		$this->assertTrue(is_array($obj->getError()) && count($obj->getError()) == 3);
	}
	/**
	 * sendError测试
	 * 待完善
	 */
	public function testSendErrorNoneAction() {
		$obj = WindErrorMessage::getInstance();
		$obj->clear();
		$this->assertEquals(null, $obj->sendError());
		$obj->addError('nameError', 'name');
		/*$obj->sendError();*/
	//	$this->assertEquals(null, $obj->sendError());
	}
	
	public function testSendErrorAction() {
		$obj = WindErrorMessage::getInstance();
		$obj->clear();
		$obj->setErrorAction(R_P . '/test/component/message/TestClass.php');
		$this->assertEquals(null, $obj->sendError());
		/*$obj->addError('nameError', 'name');
		$obj->sendError();*/
	//	$this->assertEquals(null, $obj->sendError());
	}
}
